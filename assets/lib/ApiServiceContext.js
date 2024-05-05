import React, { createContext, useContext } from "react"
import { FetchApiService } from "../services"

export const ApiServiceContext = createContext<FetchApiService | undefined>(
  undefined
)

export const useApiService = () => useContext(ApiServiceContext)

export const ApiServiceProvider = ({ baseUrl, authToken, children }) => {
  /*if (!authToken) {
    throw new Error("Can't find jwt token")
  }*/
  const apiService = new FetchApiService(baseUrl, authToken)
  return (
    <ApiServiceContext.Provider value={apiService}>
      {children}
    </ApiServiceContext.Provider>
  )
}