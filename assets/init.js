let abortSignals = new Map();

/**
 * Create Options for Micromodal.
 *
 * @param {*} [res=null] resolve function of a Promise
 * @param {*} [rej=null] reject function of a Promise
 * @return {*} 
 */
function createOptions(res = null, rej = null, eventCallback = null) {
  return {
    onShow: modal => {
      //is used to remove all event listeners at once after close
      let abortController = new AbortController();
      let form = modal.querySelector("form");

      if (eventCallback) {

        let evtarget = new EventTarget();
        evtarget.addEventListener("data", (d) => {
          eventCallback(d.detail);
        }, {
          signal: abortController.signal
        });

        modal.querySelectorAll("button[data-modal-ignore]").forEach(btn => {
          btn.addEventListener("click", (btnevent) => {
            btnevent.preventDefault();
  
            evtarget.dispatchEvent(new CustomEvent("data", {
              detail: {
                event: btnevent.target.dataset.action,
                ...createObjectFromForm(form)
              }
            }));
  
          }, {
            signal: abortController.signal
          });
        })
      }

      modal.querySelectorAll("button:not([data-modal-ignore]").forEach(btn => {

        btn.addEventListener("click", (btnevent) => {
          btnevent.preventDefault();
          MicroModal.close(modal.id);
          res({
            event: "button",
            action: btnevent.target.dataset.action,
            ...createObjectFromForm(form)
          });
        }, {
          signal: abortController.signal
        });

      });

      form.addEventListener("submit", (event) => {
        event.preventDefault();
        let data = createObjectFromForm(event.target);
        res && res(data);
        MicroModal.close(modal.id);
        form.reset();
      }, {
        signal: abortController.signal
      });

      form.addEventListener("keydown", (e) => {
        switch (e.key) {
          //User used enter to submit form. Close Window and resolve with object
          case "Enter":
            e.preventDefault();
            res && res(createObjectFromForm(form));
            MicroModal.close(modal.id);
            form.reset();
            break;

          //escape was pressed, close window and reject
          case "Escape":
            e.preventDefault();
            rej && rej();
            form.reset();
            MicroModal.close(modal.id);
            break;
        }
      }, {
        capture: false,
        signal: abortController.signal
      });

      let closeBtns = modal.querySelectorAll("[data-cancel]");
      closeBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
          form.reset();
          rej && rej();
        });
      }, {
        signal: abortController.signal
      });

      abortSignals.set(modal.id, abortController);
      //console.log(abortSignals);
    },
    onClose: (modal) => {

      //remove all remaining listeners here
      //console.log(modal);
      //console.log(abortSignals);
      abortSignals.get(modal.id).abort();
      abortSignals.delete(modal.id);
    }
  };
}

window.addEventListener("load", () => {
  MicroModal.init(createOptions());
});

/**
 * Returns the fields of a Form as Object. name-attribute becomes the object key
 * Add data-modal-ignore to the input element to skip it in conversion
 * @param {*} form
 * @return {*} 
 */
function createObjectFromForm(form) {
  let o = {
    event: "submit"
  };

  let currentTab = null;

  if (Object.keys(form.dataset).includes("hasTabs")) {
    currentTab = form.querySelector(".w-tab input[type='radio']:checked").id;
    form = form.querySelectorAll(".w-tab input[type='radio']:checked ~ .tab-content input");
  }

  for (let t of form) {
    if (Boolean(t.name) && !(Object.keys(t.dataset).includes("modalIgnore"))) {

      let value;

      // console.log(t);

      switch (t.type) {
        case "checkbox":
          value = t.checked;
          break;

        case "select-one":
          value = t.selectedOptions[0].value;
          break;

        default:
          value = t.value;
      }

      let key = Boolean(t.name) ? t.name : t.id;

      o[key] = value;
    }

  }

  o = {
    currentTab: currentTab,
    ...o,
  };

  return o;
}

/**
 * Opens a new Modal and resolves on submit. Rejects on cancel or close
 *
 * @param {string} id
 * @return {Promise<Object>} 
 */
function openModalById(id, eventCallback = null) {
  return new Promise((res, rej) => {
    let options = createOptions(res, rej, eventCallback);
    MicroModal.show(id, options);
  });
}