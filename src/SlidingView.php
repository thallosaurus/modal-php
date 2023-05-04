<?php
namespace Donstrange\Modalsupport {

  const TEST_CONTENT = '<div data-current="0">
    <div>
      Page 1
    </div>
    <div>
      Page 2
    </div>
    <div>
      Page 3
    </div>
  </div>
  <button type="button" data-modal-ignore>
    Zurück
  </button>
  <button type="button" data-modal-ignore>
    Vor
  </button>';

  class SlidingView extends TemplateLoader
  {
    public function render(): string
    {
      return TEST_CONTENT;
    }
  }
} ?>