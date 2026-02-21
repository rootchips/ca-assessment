import { describe, expect, it } from 'vitest'
import { GENERIC_SYSTEM_ERROR, resolveApiErrorMessage } from '@/utils/apiError'

describe('resolveApiErrorMessage', () => {
  it('returns generic system error for 5xx responses', () => {
    const error = {
      response: {
        status: 500,
        data: {
          message: 'Internal debug details',
        },
      },
    }

    expect(resolveApiErrorMessage(error, 'Custom fallback')).toBe(GENERIC_SYSTEM_ERROR)
  })

  it('returns backend response message for non-5xx responses', () => {
    const error = {
      response: {
        status: 422,
        data: {
          message: 'The file field is required.',
        },
      },
    }

    expect(resolveApiErrorMessage(error, 'Custom fallback')).toBe('The file field is required.')
  })

  it('returns fallback when no response message exists', () => {
    const error = {
      response: {
        status: 400,
        data: {},
      },
    }

    expect(resolveApiErrorMessage(error, 'Custom fallback')).toBe('Custom fallback')
  })
})