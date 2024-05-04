import React, { useState } from "react"
import {
  ModalExchange,
  ModalExchangeButton,
} from "../components/ui/ModalExchange"

const TradePage = () => {
  const [toggleModal, setToggleModal] = useState(false)
  return (
    <>
      <ModalExchangeButton
        toggleModal={toggleModal}
        setToggleModal={setToggleModal}
      />
      <ModalExchange
        superPowerName={"Vol"}
        description={"Capacité d'annuler la loi de la gravité"}
        category={"physique"}
        toggleModal={toggleModal}
        setToggleModal={setToggleModal}
      />
    </>
  )
}

export { TradePage }
