import React, { useState } from "react"
import clsx from "clsx"

const ModalExchangeButton = ({ toggleModal, setToggleModal }) => {
  return (
    <div className="flex justify-center m-5">
      <button
        id="readProductButton"
        data-modal-target="readProductModal"
        data-modal-toggle="readProductModal"
        className="block text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
        type="button"
        onClick={() => setToggleModal(!toggleModal)}
      >
        Echange
      </button>
    </div>
  )
}

const ModalHeader = ({ superPowerName, setToggleModal }) => {
  return (
    <div className="flex justify-between mb-4 rounded-t sm:mb-5">
      <div className="text-lg text-gray-900 md:text-xl dark:text-white">
        <h3 className="font-semibold ">Super Power</h3>
        <p className="font-bold">{superPowerName}</p>
      </div>
      <div>
        <button
          type="button"
          className="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 inline-flex dark:hover:bg-gray-600 dark:hover:text-white"
          data-modal-toggle="readProductModal"
          onClick={() => setToggleModal(false)}
        >
          <svg
            aria-hidden="true"
            className="w-5 h-5"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            ></path>
          </svg>
          <span className="sr-only">Close modal</span>
        </button>
      </div>
    </div>
  )
}

const ModalFooter = () => {
  return (
    <div className="flex justify-between items-center">
      <div className="flex items-center space-x-3 sm:space-x-4">
        <button
          type="button"
          className="text-white inline-flex items-center bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
        >
          <svg
            className="w-6 h-6 text-gray-800 dark:text-white pr-1.5"
            fill="currentColor"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15.041 13.862A4.999 4.999 0 0 1 17 17.831V21M7 3v3.169a5 5 0 0 0 1.891 3.916M17 3v3.169a5 5 0 0 1-2.428 4.288l-5.144 3.086A5 5 0 0 0 7 17.831V21M7 5h10M7.399 8h9.252M8 16h8.652M7 19h10"
            />
          </svg>
          Echanger
        </button>
      </div>
    </div>
  )
}

const ModalExchange = ({
  superPowerName,
  description,
  category,
  toggleModal,
  setToggleModal,
}) => {
  const modalClassNames =
    "overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-modal md:h-full"
  return (
    <div
      id="ModalExchange"
      tabindex="-1"
      aria-hidden={!toggleModal}
      aria-modal={toggleModal}
      role={toggleModal ? "dialog" : null}
      className={clsx(
        modalClassNames,
        { hidden: !toggleModal },
        { flex: toggleModal }
      )}
    >
      <div className="relative p-4 w-full max-w-xl h-full md:h-auto">
        <div className="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
          <ModalHeader
            superPowerName={superPowerName}
            setToggleModal={setToggleModal}
          />

          <dl>
            <dt className="mb-2 font-semibold leading-none text-gray-900 dark:text-white">
              Description
            </dt>
            <dd className="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400">
              {description}
            </dd>
            <dt className="mb-2 font-semibold leading-none text-gray-900 dark:text-white">
              Category
            </dt>
            <dd className="mb-4 font-light text-gray-500 sm:mb-5 dark:text-gray-400">
              {category}
            </dd>
          </dl>
          <ModalFooter />
        </div>
      </div>
    </div>
  )
}

export { ModalExchangeButton, ModalExchange }
