const layerid = "acteurs_ecosante";

const cc = (function () {
  let _initialized = false;  

  function insertLegend() {
    const target = document.querySelector(
      '.list-group-item[data-layerid="acteurs_ecosante"] .layerdisplay-title'
    );

    if (target && !document.querySelector("#acteursEcoLgdCustom")) {
      const lgdcustom = `
        <div id="acteursEcoLgdCustom">
          <div class="legend-item">
                <img
                src="apps/omees/ecosante/img/marker_acteur.svg"
                alt="Acteur"
                class="legend-icon"
                />
            </div>
        </div>
      `;
      target.insertAdjacentHTML("afterend", lgdcustom);
    }
}

  return {
    init: function () {
      if (_initialized) return;
      _initialized = true;
      insertLegend()
    },

    destroy: function () {
      _initialized = false;
    },
  };
})();

new CustomControl(layerid, cc.init, cc.destroy);

