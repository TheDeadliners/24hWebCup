import React from "react"
import ReactDOM from "react-dom/client"

import { Trade } from "./pages/trade"
import "./styles/app.css"

const root = ReactDOM.createRoot(document.getElementById("app-root"))

root.render(
  <React.StrictMode>
    <Trade />
  </React.StrictMode>
)
