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

  const pointHoverStyle = new ol.style.Style({
    image: new ol.style.Circle({
      radius: 9, 
      fill: new ol.style.Fill({ color: "#67c5b7" }),
      stroke: new ol.style.Stroke({ color: "#ffffff", width: 3 }),
    }),
    zIndex: 10,
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
  // --- Interaction hover (survol) ---
  const hoverSelect = new ol.interaction.Select({
    condition: ol.events.condition.pointerMove,
    layers: [layer],              // limite à cette couche
    style: pointHoverStyle,    // style appliqué aux features "hover"
  });

  mviewer.getMap().addInteraction(hoverSelect);

  // Tag la couche AVANT de l'ajouter via CustomLayer
  layer.set("layerId", LAYER_ID);

  const map = mviewer.getMap();
  const el = map.getViewport(); // plus fiable dans beaucoup d'apps que getTargetElement()

  map.on("pointermove", (evt) => {
    if (evt.dragging) return;

    // selon versions, evt.pixel peut être absent -> fallback
    const pixel = evt.pixel || map.getEventPixel(evt.originalEvent);

    const hit = map.hasFeatureAtPixel(pixel, {
      layerFilter: (l) => l && l.get("layerId") === LAYER_ID,
    });

    el.style.cursor = hit ? "pointer" : "";
  });

  handle = false;
  new CustomLayer(LAYER_ID, layer, legend);
}
