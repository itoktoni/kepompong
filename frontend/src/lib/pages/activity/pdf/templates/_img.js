const IMAGES_URL = import.meta.env.VITE_IMAGES_URL || ''
export const APP_NAME = import.meta.env.VITE_APP_NAME || 'kepompong.id'

function toRelative(url) {
  if (!url) return url
  try {
    const u = new URL(url)
    return u.pathname
  } catch {
    return url
  }
}

export function resolveImg(type, slug, filename) {
  if (!filename) return null
  if (filename.startsWith('http://') || filename.startsWith('https://')) return toRelative(filename)
  return toRelative(`${IMAGES_URL}${type}/${slug}/${filename}`)
}

export function coverImage(type, item) {
  if (!item.image) return null
  return resolveImg(type, item.slug || item.id, item.image)
}

export function pageImage(type, item, page, index) {
  const slug = item.slug || item.id
  if (page.image) return resolveImg(type, slug, page.image)
  if (page.num) return resolveImg(type, slug, page.num + '.png')
  return resolveImg(type, slug, (index + 1) + '.png')
}
