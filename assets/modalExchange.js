import React from "react"
import ReactDOM from "react-dom/client"

import { TradePage } from "./pages"
import "./styles/app.css"

const root = ReactDOM.createRoot(document.querySelector('.trade-modal'))

root.render(
  <React.StrictMode>
    <TradePage />
  </React.StrictMode>
)
