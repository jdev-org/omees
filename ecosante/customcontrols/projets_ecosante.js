const layerid = "projets_ecosante";

const cc = (function () {
  let _initialized = false;  

  function insertLegend() {
    const target = document.querySelector(
      '.list-group-item[data-layerid="projets_ecosante"] .layerdisplay-title'
    );

    if (target && !document.querySelector("#projetsEcoLgdCustom")) {
      const lgdcustom = `
        <div id="projetsEcoLgdCustom">
          <div class="legend-item">
                <img
                src="apps/ecosante/img/marker_projet.svg"
                alt="Acteur"
                class="legend-icon"
                />
           </div>
          <button class="btn btn-outline-primary btn-sm btn-custom-filter mt-2" data-bs-original-title="Filtrer les projets" onclick="filter.toggle();">
              Filtrer les projets
          </button>
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

