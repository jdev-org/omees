{
  // Définition des variables realtives à la couche.
  const GEOSERVER_URL = "https://geodata.bac-a-sable.inrae.fr/geoserver";
  const WORKSPACE = "omees";
  const LAYER = "projets_omees";
  const LAYER_URL = `${GEOSERVER_URL}/${WORKSPACE}/wfs?service=WFS&version=1.0.0&request=GetFeature&typeNames=${LAYER}&outputFormat=application/json`;
  // Définition de la variable customlayer. 
  const LAYER_ID = "projets_ecosante";  

  const ICON_SRC = "apps/omees/ecosante/img/marker_projet.svg";
  const styleCache = {};

  const strokeColor = "rgba(19, 62, 95, 1)";

  const STROKE = new ol.style.Stroke({ color: strokeColor, width: 2 });
  const STROKE_LINE = new ol.style.Stroke({ color: strokeColor, width: 3 });

  const STROKE_WHITE = new ol.style.Stroke({ color: "rgba(255, 255, 255, 1)", width: 2 });
  const STROKE_UNDER = new ol.style.Stroke({ color: "rgba(255, 255, 255, 1)", width: 7 });

  const FILL = new ol.style.Fill({ color: "rgba(19, 62, 95, 0.8)" });
  const FILL_LARGE = new ol.style.Fill({ color: "rgba(19, 62, 95, 0.02)" });

  // Styles (idem : une seule fois)
  styleCache.point ??= new ol.style.Style({
    image: new ol.style.Icon({ src: ICON_SRC, scale: 0.25 }),
  });

  styleCache.line ??= [
    new ol.style.Style({ stroke: STROKE_UNDER }),
    new ol.style.Style({ stroke: STROKE_LINE }),
  ];

  styleCache.polyLarge ??= new ol.style.Style({
    stroke: STROKE,
    fill: FILL_LARGE,
    zIndex: -10,
  });
  styleCache.polySmall ??= new ol.style.Style({ stroke: STROKE_WHITE, fill: FILL });

  const defaultStyle = function (feature) {
    const geom = feature.getGeometry();
    if (!geom) return null;

    const type = geom.getType();

    // Points
    if (type === "Point" || type === "MultiPoint") {
      return styleCache.point;
    }

    // Lignes
    if (type === "LineString" || type === "MultiLineString") {
      return styleCache.line;
    }

    // Polygones
    const aire = feature.get("visualiseur_aire_zone_etude") || 0;
    return aire > 1500000 ? styleCache.polyLarge : styleCache.polySmall;
  };

  const hoverPointStyle = new ol.style.Style({
    image: new ol.style.Icon({
      src: ICON_SRC,
      scale: 0.33
    }),
    zIndex: 10,
  });

  const hoverStyle = function (feature) {
    const geomType = feature.getGeometry().getType();
    return (geomType === "Point" || geomType === "MultiPoint")
      ? hoverPointStyle
      : null;
  };
  //Appel de la donnée
  const layer = new ol.layer.Vector({
    source: new ol.source.Vector({
      url: LAYER_URL,
      format: new ol.format.GeoJSON(),
    }),
    style: defaultStyle
  });

  layer.set("layerId", LAYER_ID);

  const map = mviewer.getMap();

  // Hover
  const hoverSelect = new ol.interaction.Select({
    condition: ol.events.condition.pointerMove,
    layers: (l) => l && l.get("layerId") === LAYER_ID,
    style: hoverStyle,
  });

  map.addInteraction(hoverSelect);

  // Curseur pointer
  map.on("pointermove", (evt) => {
    if (evt.dragging) return;

    const pixel = evt.pixel || map.getEventPixel(evt.originalEvent);

    const hit = map.hasFeatureAtPixel(pixel, {
      layerFilter: (l) => l && l.get("layerId") === LAYER_ID,
    });

    map.getViewport().style.cursor = hit ? "pointer" : "";
  });

  
  handle = false;
  new CustomLayer(LAYER_ID, layer, legend);
}
