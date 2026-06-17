# Generate Activity & Images

## Overview

### Activity Flow
```
generate:activity <type> <theme>
  → AI generates content (title, pages, moral)
  → saves to activities table (status: pending)
  → stores image prompt for later

generate:image <id>
  → reads prompt from activity
  → AI generates image
  → downloads + splits into panels
  → saves to storage/images/{type}/{slug}/
```

### Idea Flow
```
generate:idea <type>
  → AI generates game ideas (name, desc, moral)
  → saves to idea table
```

## AI Provider

All generate commands support `--provider=` and `--model=` to choose which AI to use.

### List providers

```bash
php artisan ai:provider              # list all providers
php artisan ai:provider minimax      # show models for a provider
```

### Available providers

| Provider | Base URL | Models |
|----------|----------|--------|
| `openai` (default) | api.openai.com | gpt-4o, gpt-4o-mini, gpt-3.5-turbo |
| `minimax` | api.minimax.chat | MiniMax-M2.7-highspeed, MiniMax-M2.7 |
| `deepseek` | api.deepseek.com | deepseek-chat, deepseek-coder |
| `groq` | api.groq.com | llama-3.3-70b-versatile, llama-3.1-8b-instant, mixtral-8x7b-32768 |
| `ollama` | localhost:11434 | llama3, mistral, gemma |
| `custom` | env config | custom-model |

### Usage

```bash
# Use minimax for idea generation
php artisan generate:idea permainan_edukasi --provider=minimax

# Use deepseek for activity generation with specific model
php artisan generate:activity storytelling kebersamaan --provider=deepseek --model=deepseek-chat

# Use groq for image generation
php artisan generate:image 75 --provider=groq
```

### Config

All providers are defined in `config/ai.php`:

```php
'providers' => [
    'openai' => [
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com'),
        'api_key'  => env('OPENAI_API_KEY'),
        'models'   => [
            'gpt-4o'      => ['temperature' => 0.7],
            'gpt-4o-mini' => ['temperature' => 0.7],
        ],
    ],
    // ...
],

'default_provider' => env('AI_DEFAULT_PROVIDER', 'openai'),
'default_model'    => env('AI_DEFAULT_MODEL', null),
```

### Environment variables

```bash
# Default provider (fallback)
AI_DEFAULT_PROVIDER=openai

# Per-command providers
AI_GENERATE_IDEA=minimax          # generate:idea → minimax
AI_GENERATE_ACTIVITY=openai       # generate:activity → openai
AI_GENERATE_IMAGE=openai          # generate:image → openai

# Provider API keys
OPENAI_API_KEY=sk-xxx
OPENAI_BASE_URL=https://api.openai.com
OPENAI_MODEL=gpt-4o

MINIMAX_API_KEY=sk-xxx
DEEPSEEK_API_KEY=sk-xxx
GROQ_API_KEY=gsk_xxx
OLLAMA_BASE_URL=http://localhost:11434/v1

# Image generation (separate provider)
IMAGE_API_KEY=ark-xxx
IMAGE_BASE_URL=https://ark.ap-southeast.bytepluses.com/api/v3
IMAGE_MODEL=seedream-4-5-251128
```

## Generate Activity (Content → DB)

```bash
php artisan generate:activity <type> <theme> [options]
```

### Types

| Type | Emoji | Argument |
|------|-------|----------|
| `storytelling` | 📖 | theme (e.g. kebersamaan, kejujuran) |
| `komik` | 💬 | theme (e.g. petualangan, persahabatan) |
| `coloring` | 🖍️ | theme (e.g. hewan, buah, kendaraan) |
| `worksheet` | 📝 | topic (e.g. matematika, bahasa, sains) |

### Examples

```bash
# Story with minimax
php artisan generate:activity storytelling kebersamaan --ages=5 --pages=16 --provider=minimax

# Comic with deepseek
php artisan generate:activity komik petualangan --child=Budi --pages=16 --provider=deepseek

# Coloring with groq
php artisan generate:activity coloring hewan --style=simple --pages=12 --provider=groq

# Worksheet with specific model
php artisan generate:activity worksheet matematika --subtopic=penjumlahan --grades=1 --model=gpt-4o
```

### Options

| Option | Description | Used by |
|--------|-------------|---------|
| `--provider=` | AI provider (run `ai:provider` to list) | all |
| `--model=` | AI model (run `ai:provider <provider>` to list) | all |
| `--child=` | Child name (auto-generated if empty) | all |
| `--pages=` | Number of pages/panels | all |
| `--ages=` | Target ages (e.g. 7 or 3,4,5,6,7,8) | story, comic, coloring |
| `--agama=` | Religion tag (e.g. islam, kristen) | all |
| `--subtopic=` | Worksheet subtopic (e.g. penjumlahan) | worksheet |
| `--grades=` | Target grades (e.g. 1 or 1,2,3) | worksheet |
| `--style=` | Coloring style / worksheet type | coloring, worksheet |

## Generate Idea (Game Ideas → DB)

```bash
php artisan generate:idea <type> [options]
```

### Types

| Type | Description |
|------|-------------|
| `permainan_edukasi` | Educational games |
| `permainan_kerjasama` | Team cooperation games |
| `permainan_aktif` | Active physical games |

### Examples

```bash
# Educational games with openai
php artisan generate:idea permainan_edukasi --count=10 --ages=5 --provider=openai

# Team games with groq
php artisan generate:idea permainan_kerjasama --count=8 --provider=groq --model=llama-3.3-70b-versatile

# Active games with ollama (local)
php artisan generate:idea permainan_aktif --provider=ollama --model=llama3
```

### Options

| Option | Description |
|--------|-------------|
| `--provider=` | AI provider |
| `--model=` | AI model |
| `--count=` | Number of ideas (default: 8) |
| `--ages=` | Target ages |
| `--agama=` | Religion tag |
| `--skills=` | Skills to focus on (comma-separated) |

## Generate Image (Prompt → Storage)

```bash
php artisan generate:image [id] [options]
```

### Single activity

```bash
php artisan generate:image 75
```

### Batch by type

```bash
php artisan generate:image --type=storytelling
php artisan generate:image --type=komik
php artisan generate:image --type=coloring
php artisan generate:image --type=worksheet
```

### Options

| Option | Description |
|--------|-------------|
| `--type=` | Activity type (default: storytelling) |
| `--provider=` | Image AI provider |
| `--model=` | Image model |
| `--size=` | Image size: `2K`, `1024x1024`, `512x512` (default: 2K) |
| `--pages=` | Override page count |
| `--force` | Regenerate even if exists |

### Shortcut commands

```bash
php artisan generate:coloring-image [id]    # shortcut for --type=coloring
php artisan generate:worksheet-image [id]   # shortcut for --type=worksheet
```

## Asset Config per Type

Each type defines image generation rules via `ActivityGeneratorInterface::assetConfig()`:

| Type | Mode | Default Pages | Style | Extra Rules |
|------|------|---------------|-------|-------------|
| `storytelling` | grid | 16 | Pixar 3D cartoon | No speech bubbles |
| `komik` | grid | 16 | Comic book style | Must have speech bubbles |
| `coloring` | grid | 12 | Black & white line art | No grayscale/shading |
| `worksheet` | grid | 8 | Clean educational design | Clear instructions |

## Storage Structure

```
storage/app/public/images/
├── storytelling/
│   └── kisah-kebersamaan/
│       ├── master.png      # full grid image
│       ├── cover.png       # panel 1 (cropped)
│       ├── 1.png           # panel 2
│       └── ...
├── komik/
│   └── petualangan-budi/
│       └── ...
├── coloring/
│   └── mewarnai-hewan/
│       └── ...
└── worksheet/
    └── latihan-penjumlahan/
        └── ...
```

## Full Workflow

### Activity: Generate Content → Generate Image

```bash
# 1. Check available AI providers
php artisan ai:provider

# 2. Generate story content (uses AI_GENERATE_ACTIVITY provider from .env)
php artisan generate:activity storytelling kebersamaan --ages=5 --pages=16
# Output: Saved! Activity ID: 75
#         Type  : storytelling
#         Slug  : kisah-kebersamaan
#         Image : images/storytelling/kisah-kebersamaan/

# 3. Generate image for that activity (uses AI_GENERATE_IMAGE provider from .env)
php artisan generate:image 75
# Output: Folder : images/storytelling/kisah-kebersamaan/
#         Pages  : 16 (grid: 4x4)
#         Files  : cover.png, 1.png, 2.png, ..., 15.png

# Or batch all pending stories
php artisan generate:image --type=storytelling

# Override provider per command
php artisan generate:activity komik petualangan --provider=deepseek
php artisan generate:image 75 --provider=groq
```

### Idea: Generate Game Ideas

```bash
# Generate educational game ideas (uses AI_GENERATE_IDEA provider from .env)
php artisan generate:idea permainan_edukasi --count=10 --ages=5
# Output: Saved 10 ideas to database!

# Override provider
php artisan generate:idea permainan_kerjasama --provider=groq --model=llama-3.3-70b-versatile
```

## Architecture

```
config/ai.php                           # provider registry

app/Contracts/
├── ActivityAssetInterface.php
├── ActivityGeneratorInterface.php
├── IdeaGeneratorInterface.php
└── ...

app/Services/
├── AiService.php                       # provider resolution, HTTP client
├── ActivityGeneratorService.php        # orchestrates content generators
├── ActivityAssetService.php            # orchestrates asset handlers
├── ActivityImageService.php            # image pipeline
├── ImageGeneratorService.php           # low-level AI image API
├── ImageSplitterService.php            # image splitting (GD)
├── IdeaGeneratorService.php            # orchestrates idea generators
├── ActivityGenerator/
│   ├── BaseGenerator.php
│   ├── StoryGenerator.php
│   ├── ComicGenerator.php
│   ├── ColoringGenerator.php
│   └── WorksheetGenerator.php
├── ActivityAsset/
│   ├── BaseAsset.php
│   ├── StoryAsset.php
│   ├── ComicAsset.php
│   ├── ColoringAsset.php
│   ├── WorksheetAsset.php
│   └── SingleImageAsset.php
└── IdeaGenerator/
    ├── BaseIdeaGenerator.php
    ├── EduGameGenerator.php
    ├── TeamGameGenerator.php
    └── ActiveGameGenerator.php

app/Console/Commands/
├── AiProvider.php                      # php artisan ai:provider
├── GenerateActivity.php                # content → DB
├── GenerateImage.php                   # images → storage
├── GenerateIdea.php                    # game ideas → DB
├── GenerateColoringImage.php           # shortcut
└── GenerateWorksheetImage.php          # shortcut

app/Console/Concerns/
└── UsesAiProvider.php                  # trait: --provider + --model options
```
