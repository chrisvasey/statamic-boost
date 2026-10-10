import { execFileSync } from 'node:child_process';
import { createHash } from 'node:crypto';
import { closeSync, existsSync, mkdirSync, mkdtempSync, openSync, readFileSync, rmSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

export const supportedMajors = [5, 6];
const upstream = 'statamic/docs';
const manifestPath = 'docs/upstream.json';
const bundlePath = major => `docs/statamic-docs-v${major}.md`;
const licensePath = major => `docs/STATAMIC-LICENSE-v${major}.md`;
const hashText = text => createHash('sha256').update(text).digest('hex');

function run(command, args, options = {}) {
    return execFileSync(command, args, { encoding: 'utf8', maxBuffer: 64 * 1024 * 1024, ...options }).trimEnd();
}

function api(path, args = []) {
    return JSON.parse(run('gh', ['api', path, ...args]));
}

function pages(path) {
    return api(path, ['--paginate', '--slurp']).flat();
}

export function contentHash(bundle) {
    // Source commits change for site-only edits too; only documentation changes merit a release.
    const content = bundle.replaceAll(/https:\/\/github\.com\/statamic\/docs\/blob\/[a-f0-9]{40}\//g, 'https://github.com/statamic/docs/blob/{commit}/');
    return hashText(content);
}

export function latestStable(releases) {
    return releases.filter(release => !release.draft && !release.prerelease && /^v?\d+\.\d+\.\d+$/.test(release.tag_name))
        .sort((a, b) => {
            const left = a.tag_name.replace(/^v/, '').split('.').map(Number);
            const right = b.tag_name.replace(/^v/, '').split('.').map(Number);
            return right[0] - left[0] || right[1] - left[1] || right[2] - left[2];
        })[0];
}

export function nextMinor(tag) {
    const match = /^v?(\d+)\.(\d+)\.\d+$/.exec(tag);
    if (!match) throw new Error(`Invalid stable tag: ${tag}`);
    return `v${match[1]}.${Number(match[2]) + 1}.0`;
}

export function isDocsPath(path) {
    return ['CHANGELOG.md', manifestPath, 'docs/statamic-docs-v6.0.0-beta.md', ...supportedMajors.flatMap(major => [bundlePath(major), licensePath(major)])].includes(path);
}

export function newMajors(branches, issues) {
    return [...new Set(branches.map(branch => /^(\d+)\.x$/.exec(branch.name)?.[1]).filter(Boolean).map(Number))]
        .filter(major => Number.isSafeInteger(major) && major > Math.max(...supportedMajors))
        .filter(major => !issues.some(issue => !issue.pull_request && issue.body?.includes(`<!-- statamic-docs-major:${major} -->`)))
        .sort((a, b) => a - b);
}

export function validateRecovery(tag, target, head, message, changedPaths) {
    if (target !== head || !message.split('\n').includes(`Docs-Release: ${tag}`) || changedPaths.some(path => !isDocsPath(path))) {
        throw new Error(`Tag ${tag} already exists and is not a recoverable docs release at HEAD. Inspect it before retrying; tags are never overwritten.`);
    }
}

function changedPaths(from, to = 'HEAD') {
    return run('git', ['diff', '--name-only', from, to]).split('\n').filter(Boolean);
}

function output(name, value) {
    console.log(`${name}: ${value}`);
    if (process.env.GITHUB_OUTPUT) writeFileSync(process.env.GITHUB_OUTPUT, `${name}=${value}\n`, { flag: 'a' });
}

function savePlan(path, plan) {
    writeFileSync(path, `${JSON.stringify(plan, null, 2)}\n`);
    output('changed', plan.changed);
    if (plan.blocked) output('blocked', plan.blocked);
}

function reportMajors(repository, dryRun) {
    const branches = pages(`repos/${upstream}/branches?per_page=100`);
    const issues = pages(`repos/${repository}/issues?state=all&per_page=100`);
    for (const major of newMajors(branches, issues)) {
        const title = `Add Statamic ${major} support`;
        const body = `<!-- statamic-docs-major:${major} -->\nThe upstream documentation now has a [${major}.x branch](https://github.com/${upstream}/tree/${major}.x). A docs branch may precede a stable CMS release; this does not establish compatibility.\n\n- [ ] Confirm the CMS release status and review its upgrade guide.\n- [ ] Verify MCP tools and the Boost installer.\n- [ ] Update guidelines and Composer support.\n- [ ] Add the documentation bundle and CI coverage.\n- [ ] Choose the package release version based on compatibility changes.\n\nExisting supported documentation continues to update independently. Closed tracking issues are not recreated.`;
        if (dryRun) console.log(`Would open issue: ${title}`);
        else api(`repos/${repository}/issues`, ['--method', 'POST', '-f', `title=${title}`, '-f', `body=${body}`]);
    }
}

function downloadSnapshot(major, sha, directory) {
    const archive = join(directory, `${major}.tar.gz`);
    const source = join(directory, `${major}.x`);
    const archiveFile = openSync(archive, 'w');
    try {
        execFileSync('gh', ['api', `repos/${upstream}/tarball/${sha}`], { stdio: ['ignore', archiveFile, 'pipe'] });
    } finally {
        closeSync(archiveFile);
    }
    const paths = run('tar', ['-tzf', archive]).split('\n');
    if (paths.some(path => path.startsWith('/') || path.split('/').includes('..'))) throw new Error('Unsafe upstream archive path.');
    mkdirSync(source);
    run('tar', ['-xzf', archive, '--strip-components=1', '-C', source]);
    return source;
}

export function releaseNotes(versions) {
    return `Refresh the bundled Statamic documentation from pinned upstream snapshots.\n\n${supportedMajors.map(major => {
        const version = versions[major];
        return `- Statamic ${major}: ${version.pages} pages from [${version.commit.slice(0, 12)}](https://github.com/${upstream}/commit/${version.commit}).`;
    }).join('\n')}\n\nDocumentation remains available offline through the existing search tool. No new Statamic major is enabled by this release.\n`;
}

export function prepare(repository, statePath, dryRun) {
    reportMajors(repository, dryRun);
    const release = latestStable(pages(`repos/${repository}/releases?per_page=100`));
    if (!release) throw new Error('Publish an initial stable release before enabling automatic docs releases.');
    const head = run('git', ['rev-parse', 'HEAD']);
    const tag = nextMinor(release.tag_name);
    const paths = changedPaths(release.tag_name);
    const unrelated = paths.filter(path => !isDocsPath(path));
    const blocked = unrelated.length ? `Unreleased package changes require a normal release first: ${unrelated.join(', ')}` : undefined;
    if (blocked && !dryRun) {
        savePlan(statePath, { changed: false, dryRun, blocked });
        return;
    }
    if (run('git', ['status', '--porcelain'])) throw new Error('Docs sync requires a clean checkout.');

    // Recover a release API failure after the atomic branch/tag push, before checking upstream again.
    const pendingTag = run('git', ['tag', '--list', tag]);
    if (pendingTag && !blocked) {
        const target = run('git', ['rev-parse', `${tag}^{commit}`]);
        validateRecovery(tag, target, head, run('git', ['show', '-s', '--format=%B', target]), changedPaths(release.tag_name, target));
        const manifest = JSON.parse(readFileSync(manifestPath, 'utf8'));
        savePlan(statePath, { changed: true, dryRun, recovery: true, repository, head, tag, notes: releaseNotes(manifest.versions) });
        return;
    }

    const previous = existsSync(manifestPath) ? JSON.parse(readFileSync(manifestPath, 'utf8')) : { versions: {} };
    const versions = {};
    const replacements = new Map();
    const temp = mkdtempSync(join(tmpdir(), 'statamic-docs-'));
    try {
        for (const major of supportedMajors) {
            const branch = `${major}.x`;
            const sha = api(`repos/${upstream}/branches/${branch}`).commit.sha;
            if (!/^[a-f0-9]{40}$/.test(sha)) throw new Error('Invalid upstream commit.');
            const old = previous.versions[major];
            const path = bundlePath(major);
            if (old && (!existsSync(path) || contentHash(readFileSync(path, 'utf8')) !== old.content_hash)) {
                throw new Error(`Bundle checksum mismatch: ${path}`);
            }
            if (old && (!existsSync(licensePath(major)) || hashText(readFileSync(licensePath(major), 'utf8')) !== old.license_hash)) {
                throw new Error(`License checksum mismatch for Statamic ${major}.`);
            }
            if (old?.commit === sha && !dryRun) {
                versions[major] = old;
                continue;
            }
            const source = downloadSnapshot(major, sha, temp);
            const license = readFileSync(join(source, 'LICENSE.md'), 'utf8');
            if (!license.trim()) throw new Error('Missing upstream license.');
            const licenseHash = hashText(license);
            const destination = join(temp, `bundle-${major}.md`);
            const result = JSON.parse(run('php', ['scripts/bundle-docs.php', source, String(major), sha, destination]));
            const bundle = readFileSync(destination, 'utf8');
            const hash = contentHash(bundle);
            if (!Number.isInteger(result.pages) || result.pages < 1) throw new Error(`Empty Statamic ${major} bundle.`);
            console.log(`Validated Statamic ${major}: ${result.pages} pages from ${sha}.`);
            if (old?.content_hash === hash && old?.license_hash === licenseHash) {
                versions[major] = old;
                continue;
            }
            versions[major] = { branch, commit: sha, pages: result.pages, content_hash: hash, license_hash: licenseHash };
            replacements.set(path, bundle);
            replacements.set(licensePath(major), license);
        }
        // Neither supported version is replaced until both snapshots have been validated.
        for (const [path, content] of replacements) writeFileSync(path, content);
        if (replacements.size) {
            writeFileSync(manifestPath, `${JSON.stringify({ upstream, versions }, null, 2)}\n`);
            rmSync('docs/statamic-docs-v6.0.0-beta.md', { force: true });
        }
        const changed = replacements.size > 0 || paths.some(path => isDocsPath(path) && path !== 'CHANGELOG.md');
        const notes = releaseNotes(versions);
        if (changed && !blocked) {
            const changelog = readFileSync('CHANGELOG.md', 'utf8');
            const section = `## ${tag} - ${new Date().toISOString().slice(0, 10)}\n\n${notes}\n`;
            writeFileSync('CHANGELOG.md', changelog.replace(/(?=^## )/m, section));
        }
        savePlan(statePath, { changed, dryRun, blocked, recovery: false, repository, head, tag, notes });
    } finally {
        rmSync(temp, { recursive: true, force: true });
    }
}

export function publish(statePath, dryRun) {
    const plan = JSON.parse(readFileSync(statePath, 'utf8'));
    if (!dryRun && (plan.dryRun || plan.blocked)) throw new Error('Dry-run or blocked plans cannot be published. Run live preparation after resolving the release prerequisites.');
    if (dryRun && plan.blocked) {
        console.log(`Dry run: publication blocked. ${plan.blocked}`);
        return;
    }
    if (!plan.changed) {
        if (dryRun) console.log('Dry run: no documentation changes; no release needed.');
        return;
    }
    if (dryRun) {
        console.log(`Dry run: would publish ${plan.tag}. No commit, tag or release created.`);
        return;
    }
    if (run('git', ['rev-parse', 'HEAD']) !== plan.head) throw new Error('HEAD changed after preparation; refusing to publish.');
    if (!plan.recovery) {
        const status = run('git', ['status', '--porcelain', '--untracked-files=all']);
        if (status.split('\n').filter(Boolean).some(line => !isDocsPath(line.slice(3)))) throw new Error('Unexpected files changed during validation.');
        run('git', ['config', 'user.name', 'github-actions[bot]']);
        run('git', ['config', 'user.email', '41898282+github-actions[bot]@users.noreply.github.com']);
        run('git', ['add', '--', 'docs', 'CHANGELOG.md']);
        run('git', ['commit', '--allow-empty', '-m', `Update bundled Statamic documentation\n\nDocs-Release: ${plan.tag}`]);
        run('git', ['tag', '-a', plan.tag, '-m', `Statamic documentation refresh ${plan.tag}`]);
        run('git', ['push', '--atomic', 'origin', 'HEAD:refs/heads/main', `refs/tags/${plan.tag}`]);
    }
    const notesFile = `${statePath}.notes.md`;
    writeFileSync(notesFile, plan.notes);
    run('gh', ['release', 'create', plan.tag, '--repo', plan.repository, '--verify-tag', '--title', plan.tag, '--notes-file', notesFile]);
    console.log(`Published ${plan.tag}`);
}

if (process.argv[1] && resolve(process.argv[1]) === fileURLToPath(import.meta.url)) {
    try {
        const repository = process.env.GITHUB_REPOSITORY;
        const statePath = process.env.DOCS_RELEASE_STATE;
        if (!repository || !/^[\w.-]+\/[\w.-]+$/.test(repository) || !statePath) throw new Error('GITHUB_REPOSITORY and DOCS_RELEASE_STATE are required.');
        const dryRun = process.env.DOCS_DRY_RUN === 'true';
        if (process.argv[2] === 'prepare') prepare(repository, statePath, dryRun);
        else if (process.argv[2] === 'publish') publish(statePath, dryRun);
        else throw new Error('Usage: node scripts/docs-release.mjs prepare|publish');
    } catch (error) {
        console.error(error.message);
        process.exitCode = 1;
    }
}
