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

  const layer = new ol.layer.Vector({
    source: new ol.source.Vector({
      url: LAYER_URL,
      format: new ol.format.GeoJSON({
        dataProjection: "EPSG:4326",
        featureProjection: "EPSG:3857",
      }),
    }),
    style: defaultStyle,
  });

  new CustomLayer(LAYER_ID, layer, legend);
  console.log("INIT WFS projets_ecosante", Date.now());

})();