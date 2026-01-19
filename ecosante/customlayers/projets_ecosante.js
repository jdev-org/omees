{
  // Définition des variables relatives à la couche.
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

    const collectTypes = (g) => {
      if (g.getType() === "GeometryCollection") {
        return g.getGeometries().flatMap(collectTypes);
      }
      return [g.getType()];
    };

    const types = collectTypes(geom);

    if (types.includes("Point") || types.includes("MultiPoint")) return styleCache.point;
    if (types.includes("LineString") || types.includes("MultiLineString")) return styleCache.line;

    const aire = feature.get("visualiseur_aire_zone_etude") || 0;
    return aire > 1500000 ? styleCache.polyLarge : styleCache.polySmall;
  };

  // --- STYLE HOVER (zoom icône) ---
  const hoverPointStyle = new ol.style.Style({
    image: new ol.style.Icon({ src: ICON_SRC, scale: 0.33 }), // zoom
    zIndex: 10,
  });

  // Helper : la feature contient au moins un point ? (gère GeometryCollection)
  const hasPoint = (geom) => {
    if (!geom) return false;
    const t = geom.getType();
    if (t === "Point" || t === "MultiPoint") return true;
    if (t === "GeometryCollection") return geom.getGeometries().some(hasPoint);
    return false;
  };

  // Appel de la donnée
  const layer = new ol.layer.Vector({
    source: new ol.source.Vector({
      url: LAYER_URL,
      format: new ol.format.GeoJSON(),
    }),
    style: defaultStyle,
  });

  layer.set("layerId", LAYER_ID);

  handle = false;
  new CustomLayer(LAYER_ID, layer, legend);

  // --- HOVER "light" : pas d'interaction Select, juste un setStyle temporaire ---
  const map = mviewer.getMap();

  let lastFeature = null;

  const onPointerMove = (evt) => {
    if (evt.dragging) return;

    // reset ancienne feature hoverée
    if (lastFeature) {
      lastFeature.setStyle(null); // revient au style de layer (defaultStyle)
      lastFeature = null;
    }

    const pixel = evt.pixel || map.getEventPixel(evt.originalEvent);

    const feature = map.forEachFeatureAtPixel(
      pixel,
      (f, l) => f,
      {
        layerFilter: (l) => l && l.get("layerId") === LAYER_ID,
        hitTolerance: 5, // aide à attraper l'icône
      }
    );

    const viewport = map.getViewport();
    if (!feature) {
      viewport.style.cursor = "";
      return;
    }

    // curseur pointer seulement si point
    if (!hasPoint(feature.getGeometry())) {
      viewport.style.cursor = "";
      return;
    }

    viewport.style.cursor = "pointer";
    feature.setStyle(hoverPointStyle);
    lastFeature = feature;
  };

  map.on("pointermove", onPointerMove);

  // sécurité : si on sort de la carte, on remet le style
  map.getViewport().addEventListener("mouseleave", () => {
    if (lastFeature) {
      lastFeature.setStyle(null);
      lastFeature = null;
    }
    map.getViewport().style.cursor = "";
  });
}
