{
  // Définition des variables realtives à la couche.
  const GEOSERVER_URL = "https://geodata.bac-a-sable.inrae.fr/geoserver";
  const WORKSPACE = "omees";
  const LAYER = "acteurs_omees";
  const LAYER_URL = `${GEOSERVER_URL}/${WORKSPACE}/wfs?service=WFS&version=1.0.0&request=GetFeature&typeNames=${LAYER}&outputFormat=application/json&srsName=EPSG:4326`;
  // Définition de la variable customlayer. 
  const LAYER_ID = "acteurs_ecosante";  

  // --- Styles (normal + hover) ---
  const pointSvgStyle = new ol.style.Style({
    image: new ol.style.Icon({
      src: "apps/ecosante/img/marker_acteur.svg",
      scale: 0.25,
    }),
  });

  const pointSvgHoverStyle = new ol.style.Style({
    image: new ol.style.Icon({
      src: "apps/ecosante/img/marker_acteur.svg",
      scale: 0.33, 
    }),
    zIndex: 10,
  });

  // --- Couche ---
  const layer = new ol.layer.Vector({
    source: new ol.source.Vector({
      url: LAYER_URL,
      format: new ol.format.GeoJSON({
        dataProjection: "EPSG:4326",
        featureProjection: "EPSG:3857",
      }),
    }),
    style: pointSvgStyle,
  });

  // --- Interaction hover (survol) ---
  const hoverSelect = new ol.interaction.Select({
    condition: ol.events.condition.pointerMove,
    layers: [layer],              // limite à cette couche
    style: pointSvgHoverStyle,    // style appliqué aux features "hover"
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
