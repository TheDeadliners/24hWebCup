import React from "react"
import ReactDOM from "react-dom/client"

import { ApiServiceProvider } from "./lib/ApiServiceContext"
import { getCookie } from "./lib/getCookie"
import { MarketplacePage } from "./pages"
import "./styles/app.css"

const authToken = getCookie("auth-token")
const root = ReactDOM.createRoot(document.getElementById("marketplace-main"))

root.render(
  <React.StrictMode>
    {authToken ? (
      <ApiServiceProvider baseUrl="" authToken={authToken}>
        <MarketplacePage />
      </ApiServiceProvider>
    ) : (
      <div>Not connected</div>
    )}
  </React.StrictMode>
)
