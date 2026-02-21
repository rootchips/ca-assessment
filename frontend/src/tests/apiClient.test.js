import { describe, expect, it } from 'vitest'
import { resolveApiBaseUrl } from '@/api/client'

describe('resolveApiBaseUrl', () => {
  it('returns fallback when empty', () => {
    expect(resolveApiBaseUrl('')).toBe('http://localhost:8000/api')
  })

  it('removes trailing slash', () => {
    expect(resolveApiBaseUrl('http://localhost:8000/api/')).toBe('http://localhost:8000/api')
  })
})
