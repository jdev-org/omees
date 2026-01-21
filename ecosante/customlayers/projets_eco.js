(function () {

  const LAYER_ID = "projets_ecosante";

  if (mviewer?.customLayers?.[LAYER_ID]) return;

  // Définition des variables realtives à la couche.
  const GEOSERVER_URL = "https://geodata.bac-a-sable.inrae.fr/geoserver";
  const WORKSPACE = "omees";
  const LAYER = "projets_omees";
  const LAYER_URL = `${GEOSERVER_URL}/${WORKSPACE}/wfs?service=WFS&version=1.0.0&request=GetFeature&typeNames=${LAYER}&outputFormat=application/json&srsName=EPSG:4326`;
  
  const ICON_SRC = "apps/omees/ecosante/img/marker_projet.svg";
 
  const defaultStyle = new ol.style.Style({
    image: new ol.style.Icon({
      src: ICON_SRC,
      scale: 0.25,
    }),
    stroke: new ol.style.Stroke({
      color: "rgba(19, 62, 95, 1)",
      width: 2,
    }),
    fill: new ol.style.Fill({
      color: "rgba(19, 62, 95, 0.05)",
    }),
  });

  // --- Utilisatation d'un loader personnalisé car chargement des features trop rapide issue #649
  const source = new ol.source.Vector({
    format: new ol.format.GeoJSON({
      dataProjection: "EPSG:4326",
      featureProjection: "EPSG:3857",
    }),
    loader: function (extent, resolution, projection) {
      fetch(LAYER_URL)
        .then(r => r.json())
        .then(json => {
          source.clear(true);
          const features = source.getFormat().readFeatures(json, {
            featureProjection: projection,
          });
          source.addFeatures(features);
          console.log(`[${LAYER_ID}] LOAD ${features.length} features`);
        })
        .catch(err => console.error(`[${LAYER_ID}] WFS error`, err));
    },
  });

  const layer = new ol.layer.Vector({
    source: source,
    style: defaultStyle,
  });

  new CustomLayer(LAYER_ID, layer, legend);

  let isUrlSet = false;

  // --- Mise à jour de l'URL après le chargement de la source pour le bon fonctionnement du plugin filtre
  source.on('change', function () {
    if (source.getState() === 'ready' && !isUrlSet) {
      setTimeout(function () {
        if (!isUrlSet) {
          mviewer.customLayers[LAYER_ID].layer.getSource().setUrl(LAYER_URL);
          isUrlSet = true;
        }
      }, 3000);
    }
  });
})();