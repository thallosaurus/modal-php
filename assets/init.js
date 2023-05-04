let abortSignals = new Map();

function initListener(modal, form, abortController, res, rej, eventCallback) {
  if (eventCallback) {

    let evtarget = new EventTarget();
    evtarget.addEventListener("data", (d) => {
      eventCallback({
        ...d.detail,
        form: form
      });
    }, {
      signal: abortController.signal
    });

    modal.querySelectorAll("button[data-modal-event]").forEach(btn => {
      btn.addEventListener("click", (btnevent) => {
        btnevent.preventDefault();

        let eventName = Boolean(btnevent.target.dataset.modalEvent) ? btnevent.target.dataset.modalEvent : "data";

        evtarget.dispatchEvent(new CustomEvent("data", {
          detail: {
            event: btnevent.target.dataset.action,
            eventName: eventName,
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
    // debugger;
    let data = createObjectFromForm(event.target);
    // console.log(data);
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
}

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
      let form = modal.querySelector("form");
      
      // let mutationObserver = 
      const observer = new MutationObserver((mutationList, observer) => {
        console.log(mutationList);
        
        abortSignals.get(modal.id).abort();
        abortSignals.delete(modal.id);
        
        //restart abortcontroller
        let abortController = new AbortController();
        abortSignals.set(modal.id, abortController);
        initListener(modal, form, abortController, res, rej, eventCallback);
      });
      
      const observerConfig = { attributes: true, childList: true, subtree: true };
      observer.observe(form, observerConfig);
      initListener(modal, form, abortController, res, rej, eventCallback);

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

  if (Object.keys(form.dataset).includes("hasWidget")) {
    currentTab = "tab" + form.querySelector(".w-tab > input[type='radio']:checked").dataset.tabid;
    form = form.querySelectorAll(/* ".w-tab input[type='radio']:checked ~  */".tab-content input, "/* .w-tab input[type='radio']:checked ~ */ + ".tab-content select");
  }

  for (let t of form) {
    if (Boolean(t.name) && !(Object.keys(t.dataset).includes("modalIgnore"))) {

      let value;

      // console.log(t);

      switch (t.type) {
        case "checkbox":
          // debugger;
          value = t.checked;
          break;

        case "select-one":
          // debugger;
          value = t.selectedOptions[0].value;
          break;

        case "radio":
          if (t.checked) {
            value = t.value;
          } else {
            continue;
          }
          break;

        default:
          value = t.value;
          break;
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