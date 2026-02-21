export const GENERIC_SYSTEM_ERROR = 'The system encountered an error. Please try again later.'

export const resolveApiErrorMessage = (error, fallbackMessage = GENERIC_SYSTEM_ERROR) => {
  const status = Number(error?.response?.status || 0)
  const responseMessage = error?.response?.data?.message

  if (status >= 500) {
    return GENERIC_SYSTEM_ERROR
  }

  if (typeof responseMessage === 'string' && responseMessage.trim().length > 0) {
    return responseMessage
  }

  return fallbackMessage
}