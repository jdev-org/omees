{
  // Définition des variables realtives à la couche.
  const GEOSERVER_URL = "https://geodata.bac-a-sable.inrae.fr/geoserver";
  const WORKSPACE = "omees";
  const LAYER = "acteurs_omees";
  const LAYER_URL = `${GEOSERVER_URL}/${WORKSPACE}/wfs?service=WFS&version=1.0.0&request=GetFeature&typeNames=${LAYER}&outputFormat=application/json&srsName=EPSG:4326`;
  // Définition de la variable customlayer. 
  const LAYER_ID = "acteurs_ecosante";  

  // --- Styles (normal + hover) ---
  const pointStyle = new ol.style.Style({
    image: new ol.style.Circle({
      radius: 6, 
      fill: new ol.style.Fill({ color: "#67c5b7" }),
      stroke: new ol.style.Stroke({ color: "#ffffff", width: 3 }),
    }),
  });

  const legend = {
    items: [
      {
        geometry: "Point",
        styles: [
          new ol.style.Style({
            image: new ol.style.Circle({
              fill: new ol.style.Fill({
                color: "#67c5b7",
              }),
              stroke: new ol.style.Stroke({
                color: "#ffffff",
                width: 3,
              }),
              radius: 7,
            }),
          }),
        ],
      }
    ],
  };

  // --- Couche ---
  const layer = new ol.layer.Vector({
    source: new ol.source.Vector({
      url: LAYER_URL,
      format: new ol.format.GeoJSON({
        dataProjection: "EPSG:4326",
        featureProjection: "EPSG:3857",
      }),
    }),
    style: pointStyle
  });

  handle = false;
  new CustomLayer(LAYER_ID, layer, legend);
}
