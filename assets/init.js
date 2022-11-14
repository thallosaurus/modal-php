let abortSignals = new Map();

/**
 * Create Options for Micromodal.
 *
 * @param {*} [res=null] resolve function of a Promise
 * @param {*} [rej=null] reject function of a Promise
 * @return {*} 
 */
function createOptions(res = null, rej = null) {
  return {
    onShow: modal => {
      //is used to remove all event listeners at once after close
      let abortController = new AbortController();
      let form = modal.querySelector("form");

      modal.querySelectorAll("button:not([data-modal-ignore]").forEach(btn => {

        btn.addEventListener("click", (btnevent) => {
          btnevent.preventDefault();
          // console.log(btnevent.target.dataset.action);
          MicroModal.close(modal.id);
          res({
            event: "button",
            action: btnevent.target.dataset.action,
            ...createObjectFromForm(form)
          });
        }, {
          signal: abortController.signal
        });
        
        // let data = createObjectFromForm(form);
      });

      //console.log(form);
      form.addEventListener("submit", (event) => {
        event.preventDefault();

        //data-micromodal-close does interfere with the submit listener. so we close it manually
        let data = createObjectFromForm(event.target);
        // debugger;
        // if (event.target.dataset.action =! "no-submit") {
          res && res(data);
          MicroModal.close(modal.id);
          form.reset();
        // } else {
          // MicroModal.emit('submit', data);
          // Streams
          // console.log("Stream submit", data);
        // }
      }, {
        signal: abortController.signal
      });

      form.addEventListener("keydown", (e) => {
        switch (e.key) {
          //User used enter to submit form. Close Window and resolve with object
          case "Enter":
            e.preventDefault();
            //alert("Key down")
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

          //return false;
        }
      }, {
        capture: false,
        signal: abortController.signal
      });

      let closeBtns = modal.querySelectorAll("[data-cancel]");
      closeBtns.forEach(btn => {
        //console.log(btn);
        btn.addEventListener("click", (e) => {
          //console.log("close", e);
          form.reset();
          rej && rej();
        });
      }, {
        signal: abortController.signal
      });

      abortSignals.set(modal.id, abortController);
      console.log(abortSignals);
    },
    onClose: (modal) => {
      //alert(`${modal.id} got hidden, ${trigger.id} was the trigger`);
      //alert("Closing modal");
      
      //remove all remaining listeners here
      console.log(modal);
      console.log(abortSignals);
      // debugger;
      abortSignals.get(modal.id).abort();
      abortSignals.delete(modal.id);
    }
  };
}

window.addEventListener("load", () => {
  MicroModal.init(createOptions());
  MicroModal.prototype = EventTarget;

  //console.debug("Micromodal init");
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
    // console.log(form);
    // debugger;
  }

  for (let t of form) {
    if (Boolean(t.name) && !(Object.keys(t.dataset).includes("modalIgnore"))) {
      // console.log(t);

      let value;

      if (t.type == "checkbox") {
        value = t.checked;
      } else {
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
function openModalById(id) {
  return new Promise((res, rej) => {
    let options = createOptions(res, rej);
    MicroModal.show(id, options);
  });
}