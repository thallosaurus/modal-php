let abortSignals = new Map();

function createOptions(res = null, rej = null) {
  return {
    onShow: modal => {
      let abortController = new AbortController();

      let form = modal.querySelector("form");
      //console.log(form);
      form.addEventListener("submit", (event) => {
        event.preventDefault();
        //data-micromodal-close does interfere with the submit listener. so we close it manually
        MicroModal.close(modal.id);
        res && res(createObjectFromForm(event.target));
        form.reset();
      }, {
        signal: abortController.signal
      });

      form.addEventListener("keydown", (e) => {
        switch (e.key) {
          case "Enter":
              e.preventDefault();
              //alert("Key down")
              res && res(createObjectFromForm(form));
              form.reset();
              MicroModal.close(modal.id);
              break;
          
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
    },
    onClose: (modal) => {
      //alert(`${modal.id} got hidden, ${trigger.id} was the trigger`);
      //alert("Closing modal");

      //remove all remaining listeners here
      abortSignals.get(modal.id).abort();
      abortSignals.delete(modal.id);
    }
  };
}

window.addEventListener("load", () => {
  MicroModal.init(createOptions());
  //console.debug("Micromodal init");
});

function createObjectFromForm(form) {
  let o = {};

  for (let t of form) {
    if (Boolean(t.name) && !(Object.keys(t.dataset).includes("modalIgnore"))) {
      console.log(t.dataset);
      console.log(t.name, t.value);
      o[t.name] = t.value;
    }
  }

  return o;
}

function openModalById(id) {
  return new Promise((res, rej) => {
    let options = createOptions(res, rej);
    MicroModal.show(id, options);
  });
}