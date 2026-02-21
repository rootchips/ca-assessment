import { describe, expect, it } from 'vitest'

describe('product sorting state', () => {
  it('toggles asc and desc for same column', () => {
    const filters = {
      sort_by: 'created_at',
      sort_direction: 'desc',
    }

    const applySorting = (column) => {
      if (filters.sort_by === column) {
        filters.sort_direction = filters.sort_direction === 'asc' ? 'desc' : 'asc'
      } else {
        filters.sort_by = column
        filters.sort_direction = 'asc'
      }
    }

    applySorting('brand')
    expect(filters.sort_by).toBe('brand')
    expect(filters.sort_direction).toBe('asc')

    applySorting('brand')
    expect(filters.sort_direction).toBe('desc')
  })
})
