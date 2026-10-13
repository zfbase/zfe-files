# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

ZFE Files is a single package published two ways, with the two halves talking over a small HTTP/HTML contract:

- **PHP library** (Composer `zfbase/zfe-files`, PSR-0 prefix `ZfeFiles_` → `library/`): backend for the ZFE framework (Zend Framework 1 + Doctrine 1). Models, managers, upload controller, form helpers.
- **React widget** (npm `zfe-files`, `src/` → built to `dist/`, ES module only): the file upload/preview form element.

`README.md` (Russian) covers host-app integration. `examples/` and `assets/` are reference material for host apps and are excluded from the Composer archive; they are not executed here. Code comments, docblocks and UI strings are in Russian — match that.

Releases: push a `vX.Y.Z` tag. Packagist takes the Composer version from the tag, and `.github/workflows/publish.yml` publishes the npm package with the same version (it sets `package.json` `version` from the tag, so the committed value does not matter) via npm Trusted Publishing (no npm token). It runs `npm ci`, `npm test`, then `npm publish`, where `prepack` builds `dist/`. Dist-tags: `latest` for a new version, `next` for a pre-release, `v<major>` for a version older than `latest`. Lint is not part of the CI check because of the pre-existing errors below.

## Commands

```bash
npm run dev               # vite
npm run build             # tsc -b && vite build  → dist/index.js, index.css, index.d.ts (rolled-up types)
npm run lint              # eslint .
npm test                  # jest (ts-jest, specs live next to sources: *.spec.ts)
npx jest path/to/file.spec.ts            # one file
npx jest -t "parseState"                 # one test by name
npm run storybook         # port 6006

composer lint             # php-cs-fixer --dry-run --diff (config: .php_cs, PSR-2 + PHP71Migration)
composer fix
```

- `npm run lint` currently reports 2 pre-existing errors (`{}` type in `src/hooks/useCollection.ts`) plus warnings; don't treat those as regressions.
- There is no PHP test suite and the PHP runtime dependencies (ZFE, Doctrine, Zend) are not installed here (`vendor/` only has php-cs-fixer), so PHP code can't be executed or type-checked in this repo.

## Architecture

### PHP: how a file is modelled

- **Manageable record** (`ZfeFiles_Manageable`): a host model that owns files. It declares `getFileSchemas()` returning a `ZfeFiles_Schema_Collection`. The `ZfeFiles_Model_Injection` trait supplies agent storage and `_filesToArray` / `_filesFromArray` / `_filesPostInsert` / `_filesPostSave`, which the record is expected to call from its own Doctrine `toArray`/`fromArray`/`postInsert`/`postSave` hooks.
- **Schema** (`Schema/Default|Image|Audio|Video`): one attachment slot on a record — `code` (also the form field name), file `model`, `multiple`, `accept`, optional `handler`. `getOptions()` is driven by `$optionKeys`; subclasses append to it.
- **File model**: host-defined Doctrine model implementing `ZfeFiles_File_OriginInterface` (`getManager()`, real/web path helpers). On disk: `<files.root>/<id split into chunks>/<hash>.<ext>`.
- **Manager** decides the data model — pick one per file model:
  - `Manager_Mono`: the file row itself holds `model_name` / `item_id` / `schema_code`; a file belongs to exactly one record.
  - `Manager_Multiple`: many-to-many through a mediator model (default `FilesMediator`); files are de-duplicated by hash, and per-link data (e.g. crop box) lives on the mediator.
- **Agent** (`Agent_Mono` / `Agent_Multiple`): wraps file (+ mediator) and is the unit everything else passes around. `getDataForUploader()` is the JSON shape the frontend receives. `process()` runs the manager's handler, then the schema's (`Handler_DefaultTask` plans a ZFE deferred task).

### Request/data flow

1. **Render**: `ZfeFiles_Form_Helpers` trait (`addFileAjaxElement` and the image/audio/video variants) merges schema options into a `fileAjax*` form element; view helper `FormFileAjax` emits `<div class="zfe-files-ajax" data-*>` containing one hidden `<input name="field[i]" value="<fileId>" data-*>` per existing file.
2. **Mount**: the host app (not this repo — nothing in `src/` mounts anything) calls `getFileAjaxProps(rootEl, custom)` and renders `<FileAjaxElement>`. `getFileAjaxProps` turns the root's `data-*` attributes (kebab → camelCase; `itemId`/`maxChunkSize`/`maxFileSize` → int) and the hidden inputs' `data-*` into props. PHP and TS therefore must agree on attribute names (see `attributesForModify` in `FormFileAjax.php`).
3. **Upload**: `createUploader` uses `SimpleUploader` when the file is below `maxChunkSize` (default 1 MiB), otherwise `ChunksUploader`, which posts `chunkNum`/`chunksCount`/`uid`. `ZfeFiles_Controller_Default::uploadAction` → `Uploader_DefaultAjax` reassembles chunks via a PHP session namespace and **requires them strictly in order** (that's why `maxThreads` effectively stays 1) → `manager->factory()` (hash, save row, move from temp, chmod/chown) → `agent->process()`. Responses use the ZFE JSON envelope; the chunk uploader requires `status` to be `0`. Upload result keys starting with `data…` become `FileItem.data` (`parseUploadResult`).
4. **Submit**: `Storage` writes hidden inputs `name[]` back into the form — plain id (`LiteRender`) or `JSON.stringify({id, ...data})` when the item has `data` (`ExtendedRender`). On the PHP side `Manager_Abstract::extractIds/extractData` accept either form, and `_filesFromArray` → `createAgents`.
5. **Auto-save**: when `itemId`, `modelName`, `schemaCode`, `linkUrl` and `unlinkUrl` are all present (form helper option `autoSave`), delete/undelete in the widget immediately POST to `linkAction`/`unlinkAction` instead of waiting for form submit.

### Frontend layout

- `src/main.ts` is the whole public API: `getFileAjaxProps`, `FileAjaxElement`, `ProgressCircle`, `createUploader`, `pageUnload`.
- `FileAjaxPreview` picks the preview by `type` (`image` | `audio` | `video`, default simple file); a `previewRender` prop overrides it. The image preview contains a hand-written cropper under `Preview/Image/components/cropper/`.
- State is a local `useCollection` hook (items get a `nanoid` `key`; `id` only exists after upload). Items are never removed on delete — they're flagged `deleted` and excluded from `Storage`.
- CSS classes are prefixed `zfe-files-ajax-*`; markup relies on host-app Bootstrap classes. Dev deps/Storybook still load Bootstrap 3.4 CSS while the error dismiss button uses Bootstrap 5's `.btn-close`, so it renders unstyled in Storybook.
- `vite.config.ts` externalizes react, react-dom, react-dropzone, react-modal, classnames; everything else is bundled. `nanoid` is imported but not declared in `package.json` (resolves transitively) and ends up bundled.

## Gotchas

- **Config keys are inconsistent** — trust the reading code, not `assets/example.ini`: the manager reads `files.fileModelName` (example.ini says `modelName`); the upload temp dir is `files.tempPath` in the uploader but `files.tempRoot` in `Task_Download`; example.ini names a `Uploader_Handler_Plupload` class that doesn't exist (only `Handler_Default` does).
- **Docs/examples name interfaces that don't exist**: README mentions `ZfeFiles_UserFileInterface` and `examples/models/Files.php` implements `ZfeFiles_FileInterface`; the real ones are `ZfeFiles_File_OriginInterface` / `ZfeFiles_File_Interface`.
- PHP file/class naming follows PSR-0 underscore mapping: `ZfeFiles_Foo_Bar` ↔ `library/ZfeFiles/Foo/Bar.php`. Every PHP file starts with the standard header comment enforced by `.php_cs`.
