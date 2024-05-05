class FormError extends Error {
  constructor(message, status, response) {
    super(message)
    this.name = "FormError"
    this.status = status
    this.response = response
  }
}

export class FetchApiService {
  constructor(baseUrl, authToken) {
    this.baseUrl = baseUrl
    this.authToken = authToken
  }

  async fetchWithAuth(url, options) {
    const headers = {
      Authorization: `Bearer ${this.authToken}`, // Include bearer token in the header
      ...options?.headers,
    }

    return await fetch(url, { ...options, headers })
  }

  async getSuperPowers() {
    const response = await this.fetchWithAuth(
      `${this.baseUrl}/rest/superpowers`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      }
    )
    if (!response.ok) {
      const responseData = await response.json()

      throw new FormError(
        "Failed to getSuperPowers",
        response.status,
        responseData ? responseData : {}
      )
    }
    return
  }

  async getTradeRequests() {
    const response = await this.fetchWithAuth(
      `${this.baseUrl}/rest/traderequests`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(company),
      }
    )
    if (!response.ok) {
      const responseData = await response.json()

      throw new FormError(
        "Failed to getTradeRequests",
        response.status,
        responseData ? responseData : {}
      )
    }
    return
  }
}
