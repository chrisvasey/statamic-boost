import assert from 'node:assert/strict';
import { spawnSync } from 'node:child_process';
import { createHash } from 'node:crypto';
import { chmodSync, mkdirSync, mkdtempSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { delimiter, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import test from 'node:test';
import { contentHash, isDocsPath, latestStable, newMajors, nextMinor, releaseNotes, validateRecovery } from '../docs-release.mjs';

const head = 'a'.repeat(40);
const otherCommit = 'b'.repeat(40);
const script = fileURLToPath(new URL('../docs-release.mjs', import.meta.url));

test('content hashes ignore source commit changes but retain documentation and URL changes', () => {
    const bundle = `# Example\nSource: https://github.com/statamic/docs/blob/${head}/content/example.md\nBody\n`;
    assert.equal(contentHash(bundle), contentHash(bundle.replace(head, otherCommit)));
    assert.notEqual(contentHash(bundle), contentHash(bundle.replace('Body', 'Updated body')));
    assert.notEqual(contentHash(bundle), contentHash(bundle.replace('example.md', 'other.md')));
    assert.notEqual(contentHash(head), contentHash(otherCommit));
});

test('the latest stable release uses numeric versions and skips drafts, prereleases and invalid tags', () => {
    const releases = ['v2.9.20', 'v2.10.0', '2.10.12', 'v2.10.9', 'v1.99.99', 'latest', 'v20.0.0-beta.1']
        .map(tag_name => ({ tag_name }));
    releases.push({ tag_name: 'v30.0.0', draft: true }, { tag_name: 'v40.0.0', prerelease: true });
    assert.equal(latestStable(releases).tag_name, '2.10.12');
    assert.equal(latestStable([...releases, { tag_name: 'v3.0.0' }]).tag_name, 'v3.0.0');
    assert.equal(latestStable([]), undefined);
    assert.equal(latestStable([{ tag_name: 'v1.0' }, { tag_name: 'v1.0.0', draft: true }]), undefined);
});

test('minor releases retain the major and reset the patch', () => {
    assert.equal(nextMinor('v1.9.27'), 'v1.10.0');
    assert.equal(nextMinor('2.0.8'), 'v2.1.0');
    for (const tag of ['v1.2', 'v1.2.3-beta.1', 'v1.2.3+build', 'release-1.2.3']) {
        assert.throws(() => nextMinor(tag), /Invalid stable tag/);
    }
});

test('the docs allowlist accepts only supported bundles, their manifest and changelog', () => {
    for (const path of ['CHANGELOG.md', 'docs/upstream.json', 'docs/statamic-docs-v5.md', 'docs/statamic-docs-v6.md', 'docs/STATAMIC-LICENSE-v5.md', 'docs/STATAMIC-LICENSE-v6.md', 'docs/statamic-docs-v6.0.0-beta.md']) {
        assert.equal(isDocsPath(path), true, path);
    }
    for (const path of ['src/ServiceProvider.php', 'composer.json', '.github/workflows/update-docs.yml', 'docs/README.md', 'docs/statamic-docs-v7.md', 'docs/STATAMIC-LICENSE-v7.md', '../CHANGELOG.md', 'docs/../CHANGELOG.md']) {
        assert.equal(isDocsPath(path), false, path);
    }
});

test('new major discovery uses exact branches and deduplicates markers in open or closed issues, never PRs', () => {
    const branches = ['5.x', '6.x', '7.x', '7.x', '8.x', '9.x', '10.x', 'main', '11.x-preview', '12.0', 'v13.x', 'feature/14.x', '9007199254740993.x']
        .map(name => ({ name }));
    const issues = [
        { state: 'closed', body: '<!-- statamic-docs-major:7 -->' },
        { state: 'open', body: 'Already tracked\n<!-- statamic-docs-major:8 -->' },
        { body: '<!-- statamic-docs-major:9 -->', pull_request: {} },
        { body: null },
        { body: '<!-- statamic-docs-major:100 -->' },
    ];
    assert.deepEqual(newMajors(branches, issues), [9, 10]);
});

test('pending tags are recoverable only at the same HEAD with an exact ownership marker and docs-only changes', () => {
    const tag = 'v1.3.0';
    const message = `Update documentation\n\nDocs-Release: ${tag}\n`;
    const paths = ['CHANGELOG.md', 'docs/upstream.json', 'docs/statamic-docs-v6.md'];
    assert.doesNotThrow(() => validateRecovery(tag, head, head, message, paths));
    for (const [target, body, changed] of [
        [otherCommit, message, paths],
        [head, 'A manually created release', paths],
        [head, 'Docs-Release: v1.2.0', paths],
        [head, `Prefix Docs-Release: ${tag}`, paths],
        [head, `Docs-Release: ${tag} suffix`, paths],
        [head, message, [...paths, 'src/Mcp/Tools/SearchDocs.php']],
    ]) {
        assert.throws(() => validateRecovery(tag, target, head, body, changed), /not a recoverable docs release/);
    }
});

test('release notes retain the upstream commit and page provenance for each supported major', () => {
    const notes = releaseNotes({ 5: { commit: head, pages: 125 }, 6: { commit: otherCommit, pages: 250 } });
    assert.ok(notes.includes(`Statamic 5: 125 pages from [${head.slice(0, 12)}](https://github.com/statamic/docs/commit/${head})`));
    assert.ok(notes.includes(`Statamic 6: 250 pages from [${otherCommit.slice(0, 12)}](https://github.com/statamic/docs/commit/${otherCommit})`));
    assert.match(notes, /No new Statamic major is enabled/);
});

function fixture(t, mode = 'unchanged') {
    const directory = mkdtempSync(join(tmpdir(), 'docs-release-test-'));
    t.after(() => rmSync(directory, { recursive: true, force: true }));
    mkdirSync(join(directory, 'bin'));
    mkdirSync(join(directory, 'docs'));
    const statePath = join(directory, 'state.json');
    const callsPath = join(directory, 'calls.jsonl');
    const versions = {};
    for (const major of [5, 6]) {
        const bundle = `Statamic ${major} documentation\n`;
        const license = `Fixture license for Statamic ${major}\n`;
        writeFileSync(join(directory, `docs/statamic-docs-v${major}.md`), bundle);
        writeFileSync(join(directory, `docs/STATAMIC-LICENSE-v${major}.md`), license);
        versions[major] = { branch: `${major}.x`, commit: head, pages: major * 10, content_hash: contentHash(bundle), license_hash: createHash('sha256').update(license).digest('hex') };
    }
    writeFileSync(join(directory, 'docs/upstream.json'), JSON.stringify({ versions }));
    writeFileSync(join(directory, 'CHANGELOG.md'), '# Changelog\n\n## v1.2.3\n');
    const fakeCli = `#!/usr/bin/env node
const fs = require('node:fs');
const command = require('node:path').basename(process.argv[1]);
const args = process.argv.slice(2);
const mode = process.env.TEST_MODE;
fs.appendFileSync(process.env.TEST_CALLS, JSON.stringify([command, ...args]) + '\\n');
let result;
if (command === 'gh' && args[0] === 'api') {
    const path = args[1];
    if (path === 'repos/statamic/docs/branches?per_page=100') result = [[{name:'5.x'}, {name:'6.x'}, {name:'7.x'}]];
    else if (path.includes('/issues?')) result = [[]];
    else if (path === 'repos/owner/package/issues' && args.includes('POST')) result = {number: 1};
    else if (path.includes('/releases?')) result = [[{tag_name:'v1.2.3'}]];
    else if (/^repos\\/statamic\\/docs\\/branches\\/[56]\\.x$/.test(path)) result = {commit:{sha: mode === 'updated' ? '${otherCommit}' : '${head}'}};
    else if (path.startsWith('repos/statamic/docs/tarball/')) result = 'Fixture archive';
    if (result !== undefined && typeof result !== 'string') result = JSON.stringify(result);
} else if (command === 'gh' && args[0] === 'release' && args[1] === 'create') result = '';
else if (command === 'git') {
    if (args[0] === 'rev-parse') result = '${head}';
    else if (args[0] === 'diff') result = mode === 'recovery' ? 'docs/upstream.json' : mode === 'blocked' ? 'src/ServiceProvider.php' : '';
    else if (args[0] === 'status') result = mode === 'changed' ? ' M docs/statamic-docs-v5.md\\n M CHANGELOG.md\\n' : mode === 'code' ? ' M src/ServiceProvider.php\\n' : '';
    else if (args[0] === 'tag' && args[1] === '--list') result = mode === 'recovery' ? 'v1.3.0' : '';
    else if (args[0] === 'show') result = 'Update docs\\n\\nDocs-Release: v1.3.0\\n';
    else if (['config', 'add', 'commit', 'tag', 'push'].includes(args[0])) result = '';
} else if (command === 'tar') {
    if (args[0] === '-tzf') result = 'upstream/LICENSE.md\\nupstream/content/example.md\\n';
    else if (args[0] === '-xzf') {
        const source = args[args.indexOf('-C') + 1];
        const major = require('node:path').basename(source).split('.')[0];
        fs.writeFileSync(require('node:path').join(source, 'LICENSE.md'), 'Fixture license for Statamic ' + major + '\\n');
        result = '';
    }
} else if (command === 'php' && args[0] === 'scripts/bundle-docs.php') {
    fs.writeFileSync(args[4], (mode === 'updated' ? 'Updated ' : '') + 'Statamic ' + args[2] + ' documentation\\n');
    result = JSON.stringify({pages: Number(args[2]) * 10});
}
if (result === undefined) { console.error('Unexpected fake CLI call: ' + JSON.stringify([command, ...args])); process.exit(99); }
process.stdout.write(result);
`;
    for (const command of ['gh', 'git', 'tar', 'php']) {
        const path = join(directory, 'bin', command);
        writeFileSync(path, fakeCli);
        chmodSync(path, 0o755);
    }
    return {
        directory,
        statePath,
        savePlan(overrides = {}) {
            writeFileSync(statePath, JSON.stringify({ changed: true, dryRun: false, recovery: false, repository: 'owner/package', head, tag: 'v1.3.0', notes: releaseNotes(versions), ...overrides }));
        },
        run(action, dryRun = true) {
            return spawnSync(process.execPath, [script, action], {
                cwd: directory,
                encoding: 'utf8',
                env: { ...process.env, PATH: `${join(directory, 'bin')}${delimiter}${process.env.PATH}`, GITHUB_OUTPUT: '', GITHUB_REPOSITORY: 'owner/package', DOCS_RELEASE_STATE: statePath, DOCS_DRY_RUN: String(dryRun), TEST_MODE: mode, TEST_CALLS: callsPath },
            });
        },
        calls() {
            try {
                return readFileSync(callsPath, 'utf8').trim().split('\n').filter(Boolean).map(line => JSON.parse(line));
            } catch (error) {
                if (error.code === 'ENOENT') return [];
                throw error;
            }
        },
    };
}

test('dry-run rebuilds both unchanged snapshots, prepares no release and only previews a new-major issue', t => {
    const context = fixture(t);
    const result = context.run('prepare');
    assert.equal(result.status, 0, result.stderr);
    assert.match(result.stdout, /Would open issue: Add Statamic 7 support/);
    const plan = JSON.parse(readFileSync(context.statePath));
    assert.equal(plan.changed, false);
    assert.equal(plan.dryRun, true);
    assert.equal(context.calls().filter(call => call[0] === 'gh' && call[2]?.includes('/tarball/')).length, 2);
    assert.deepEqual(context.calls().filter(call => call[0] === 'php').map(call => call[3]), ['5', '6']);
    const callsBeforePublish = context.calls();
    const published = context.run('publish');
    assert.equal(published.status, 0, published.stderr);
    assert.match(published.stdout, /no.*(?:change|release)/i);
    assert.deepEqual(context.calls(), callsBeforePublish);
    assert.ok(!context.calls().some(call => call.includes('POST')));
});

test('a pushed pending release prepares recovery and dry-run publish performs no CLI operations', t => {
    const context = fixture(t, 'recovery');
    const prepared = context.run('prepare');
    assert.equal(prepared.status, 0, prepared.stderr);
    const plan = JSON.parse(readFileSync(context.statePath));
    assert.equal(plan.changed, true);
    assert.equal(plan.recovery, true);
    assert.equal(plan.dryRun, true);
    assert.equal(plan.tag, 'v1.3.0');
    const callsBeforePublish = context.calls();
    const published = context.run('publish');
    assert.equal(published.status, 0, published.stderr);
    assert.match(published.stdout, /Dry run: would publish v1\.3\.0/);
    assert.deepEqual(context.calls(), callsBeforePublish);
    const live = fixture(t, 'recovery');
    const livePrepared = live.run('prepare', false);
    assert.equal(livePrepared.status, 0, livePrepared.stderr);
    assert.equal(JSON.parse(readFileSync(live.statePath)).dryRun, false);
    const callsBeforeRecovery = live.calls();
    const recovered = live.run('publish', false);
    assert.equal(recovered.status, 0, recovered.stderr);
    assert.deepEqual(live.calls().slice(callsBeforeRecovery.length).map(call => call.slice(0, 2)), [['git', 'rev-parse'], ['gh', 'release']]);
});

test('blocked dry-run still rebuilds both snapshots, while blocked live preparation skips downloads', t => {
    for (const dryRun of [true, false]) {
        const context = fixture(t, 'blocked');
        const prepared = context.run('prepare', dryRun);
        assert.equal(prepared.status, 0, prepared.stderr);
        const plan = JSON.parse(readFileSync(context.statePath));
        assert.equal(plan.dryRun, dryRun);
        assert.equal(plan.changed, false);
        assert.match(plan.blocked, /src\/ServiceProvider\.php/);
        assert.equal(context.calls().filter(call => call[0] === 'gh' && call[2]?.includes('/tarball/')).length, dryRun ? 2 : 0);
        assert.deepEqual(context.calls().filter(call => call[0] === 'php').map(call => call[3]), dryRun ? ['5', '6'] : []);
        assert.equal(context.calls().some(call => call.includes('POST')), !dryRun);
        const callsBeforePublish = context.calls();
        const preview = context.run('publish');
        assert.equal(preview.status, 0, preview.stderr);
        assert.match(preview.stdout, /blocked/i);
        assert.deepEqual(context.calls(), callsBeforePublish);
    }
});

test('dry-run prepares changed documentation and previews its minor release without publishing', t => {
    const context = fixture(t, 'updated');
    const prepared = context.run('prepare');
    assert.equal(prepared.status, 0, prepared.stderr);
    const plan = JSON.parse(readFileSync(context.statePath));
    assert.equal(plan.dryRun, true);
    assert.equal(plan.changed, true);
    assert.equal(plan.tag, 'v1.3.0');
    const callsBeforePublish = context.calls();
    const preview = context.run('publish');
    assert.equal(preview.status, 0, preview.stderr);
    assert.match(preview.stdout, /would publish v1\.3\.0/i);
    assert.deepEqual(context.calls(), callsBeforePublish);
    assert.ok(!context.calls().some(call => call.includes('POST') || ['commit', 'push'].includes(call[1])));
});

test('live publish rejects preview and blocked plans before any CLI operation, even without changed docs', t => {
    for (const changed of [true, false]) {
        for (const overrides of [{ dryRun: true }, { blocked: 'Unreleased package changes require a normal release first.' }]) {
            const context = fixture(t);
            context.savePlan({ changed, ...overrides });
            const result = context.run('publish', false);
            assert.equal(result.status, 1, result.stderr);
            assert.match(result.stderr, /dry.run|preview|block|unreleased/i);
            assert.deepEqual(context.calls(), []);
        }
    }
});

test('prepare rejects changed bundle or license contents even when their upstream commit is unchanged', t => {
    for (const [path, expected] of [['docs/statamic-docs-v5.md', /Bundle checksum mismatch/], ['docs/STATAMIC-LICENSE-v5.md', /License checksum mismatch/]]) {
        const context = fixture(t);
        writeFileSync(join(context.directory, path), 'Unexpected content');
        const result = context.run('prepare');
        assert.equal(result.status, 1);
        assert.match(result.stderr, expected);
    }
});

test('normal publish accepts unstaged docs paths and pushes the branch and tag atomically before release creation', t => {
    const context = fixture(t, 'changed');
    context.savePlan();
    const result = context.run('publish', false);
    assert.equal(result.status, 0, result.stderr);
    const calls = context.calls();
    const push = calls.findIndex(call => call[0] === 'git' && call[1] === 'push');
    const release = calls.findIndex(call => call[0] === 'gh' && call[1] === 'release');
    assert.deepEqual(calls[push], ['git', 'push', '--atomic', 'origin', 'HEAD:refs/heads/main', 'refs/tags/v1.3.0']);
    assert.ok(release > push);
    assert.ok(calls[release].includes('--verify-tag'));
    assert.ok(calls.some(call => call[1] === 'commit' && call.includes('Update bundled Statamic documentation\n\nDocs-Release: v1.3.0')));
});

test('publish refuses code changes or a changed HEAD before committing or pushing', t => {
    for (const [mode, overrides, expected] of [['code', {}, /Unexpected files/], ['changed', { head: otherCommit }, /HEAD changed/]]) {
        const context = fixture(t, mode);
        context.savePlan(overrides);
        const result = context.run('publish', false);
        assert.equal(result.status, 1);
        assert.match(result.stderr, expected);
        assert.ok(context.calls().every(call => call[0] === 'git' && ['rev-parse', 'status'].includes(call[1])));
    }
});
