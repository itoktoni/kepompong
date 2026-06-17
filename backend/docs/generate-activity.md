# Generate Activity & Images

## Overview

```
generate:activity  →  AI text content  →  save to DB (status: pending)
generate:image     →  AI image prompt   →  generate → download → split → save to storage
```

## Step 1: Generate Content (Text → DB)

```bash
php artisan generate:activity <type> <theme> [options]
```

### Types

| Type | Emoji | Service | Argument |
|------|-------|---------|----------|
| `storytelling` | 📖 | StoryGeneratorService | theme (e.g. kebersamaan, kejujuran) |
| `komik` | 💬 | ComicGeneratorService | theme (e.g. petualangan, persahabatan) |
| `coloring` | 🖍️ | ColoringGeneratorService | theme (e.g. hewan, buah, kendaraan) |
| `worksheet` | 📝 | WorksheetGeneratorService | topic (e.g. matematika, bahasa, sains) |

### Examples

```bash
# Story
php artisan generate:activity storytelling kebersamaan --ages=5 --pages=16

# Comic
php artisan generate:activity komik petualangan --child=Budi --pages=16

# Coloring
php artisan generate:activity coloring hewan --style=simple --pages=12

# Worksheet
php artisan generate:activity worksheet matematika --subtopic=penjumlahan --grades=1 --pages=8
```

### Options

| Option | Description | Used by |
|--------|-------------|---------|
| `--child=` | Child name (auto-generated if empty) | all |
| `--pages=` | Number of pages/panels | all |
| `--ages=` | Target ages (e.g. 7 or 3,4,5,6,7,8) | story, comic, coloring |
| `--agama=` | Religion tag (e.g. islam, kristen) | all |
| `--subtopic=` | Worksheet subtopic (e.g. penjumlahan) | worksheet |
| `--grades=` | Target grades (e.g. 1 or 1,2,3) | worksheet |
| `--style=` | Coloring style / worksheet type | coloring, worksheet |

### What it does

1. Calls AI service to generate content (title, pages/items, moral, etc.)
2. Builds prompt for image generation
3. Saves to `activities` table with `status: pending`
4. Output shows: Activity ID, type, slug, image folder path

## Step 2: Generate Images (Prompt → Storage)

```bash
php artisan generate:image [id] [options]
```

### Single activity by ID

```bash
php artisan generate:image 75
```

This will:
1. Find activity 75 in database
2. Read its `type`, `slug`, and `prompt`
3. Generate image via AI using the stored prompt
4. Download the generated image
5. Split into panels (e.g. 16 panels = 4x4 grid)
6. Save to `storage/app/public/images/{type}/{slug}/`

### Batch by type

```bash
php artisan generate:image --type=storytelling
php artisan generate:image --type=komik
php artisan generate:image --type=coloring
php artisan generate:image --type=worksheet
```

Processes all pending activities of that type.

### Options

| Option | Description |
|--------|-------------|
| `--type=` | Activity type (default: storytelling) |
| `--model=` | Image model (default: from IMAGE_MODEL env) |
| `--size=` | Image size: `2K`, `1024x1024`, `512x512` (default: 2K) |
| `--pages=` | Override page count for splitting |
| `--force` | Regenerate even if image folder already exists |

### Shortcut commands

```bash
# Same as: php artisan generate:image --type=coloring
php artisan generate:coloring-image [id]

# Same as: php artisan generate:image --type=worksheet
php artisan generate:worksheet-image [id]
```

## Asset Config per Type

Each type defines its own image generation rules via `ActivityGeneratorInterface::assetConfig()`:

| Type | Mode | Default Pages | Style | Extra Rules |
|------|------|---------------|-------|-------------|
| `storytelling` | grid | 16 | Pixar 3D cartoon | No speech bubbles |
| `komik` | grid | 16 | Comic book style | Must have speech bubbles |
| `coloring` | grid | 12 | Black & white line art | No grayscale/shading |
| `worksheet` | grid | 8 | Clean educational design | Clear instructions |

Access via:
```php
app(ActivityGeneratorService::class)->assetConfig('storytelling');
// ['mode' => 'grid', 'default_pages' => 16, 'image_size' => '2K', 'style' => '...', 'extra_rules' => '...']
```

## Storage Structure

```
storage/app/public/images/
├── storytelling/
│   └── kisah-kebersamaan/
│       ├── master.png      # full grid image
│       ├── cover.png       # panel 1 (cropped)
│       ├── 1.png           # panel 2
│       ├── 2.png           # panel 3
│       └── ...
├── komik/
│   └── petualangan-budi/
│       ├── master.png
│       ├── cover.png
│       └── ...
├── coloring/
│   └── mewarnai-hewan/
│       └── ...
└── worksheet/
    └── latihan-penjumlahan/
        └── ...
```

## Full Workflow Example

```bash
# 1. Generate story content
php artisan generate:activity storytelling kebersamaan --ages=5 --pages=16
# Output: Saved! Activity ID: 75

# 2. Generate images for that activity
php artisan generate:image 75
# Output: Saved to: images/storytelling/kisah-kebersamaan/

# Or batch all pending stories
php artisan generate:image --type=storytelling
```

## Architecture

```
app/Console/Commands/
├── GenerateActivity.php         # Step 1: content → DB
├── GenerateImage.php            # Step 2: images → storage
├── GenerateColoringImage.php    # shortcut: generate:image --type=coloring
└── GenerateWorksheetImage.php   # shortcut: generate:image --type=worksheet

app/Services/
├── ActivityGeneratorService.php        # orchestrates generators
├── ActivityImageService.php            # image pipeline: generate → download → split
├── ImageGeneratorService.php           # low-level AI image API
├── ImageSplitterService.php            # low-level image splitting (GD)
└── ActivityGenerator/
    ├── ActivityGeneratorInterface.php   # interface: generateContent, buildActivityData, buildPrompt, assetConfig
    ├── BaseGenerator.php               # shared helpers
    ├── StoryGenerator.php              # storytelling
    ├── ComicGenerator.php              # komik
    ├── ColoringGenerator.php           # coloring
    └── WorksheetGenerator.php          # worksheet
```
