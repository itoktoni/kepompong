const IMAGES_URL = import.meta.env.VITE_IMAGES_URL || ''
export const APP_NAME = import.meta.env.VITE_APP_NAME || 'kepompong.id'

export function resolveImg(type, slug, filename) {
  if (!filename) return null
  let url = filename
  if (!filename.startsWith('http://') && !filename.startsWith('https://')) {
    url = `${IMAGES_URL}${type}/${slug}/${filename}`
  }
  return url
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
