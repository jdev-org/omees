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

    // Récupère tous les types, y compris dans les GeometryCollection
    const collectTypes = (g) => {
      if (g.getType() === "GeometryCollection") {
        return g.getGeometries().flatMap(collectTypes);
      }
      return [g.getType()];
    };

    const types = collectTypes(geom);

    // Points (un seul style pour tous les points de la collection)
    if (types.includes("Point") || types.includes("MultiPoint")) {
      return styleCache.point;
    }

    // Lignes
    if (types.includes("LineString") || types.includes("MultiLineString")) {
      return styleCache.line;
    }

    // Polygones
    const aire = feature.get("visualiseur_aire_zone_etude") || 0;
    return aire > 1500000 ? styleCache.polyLarge : styleCache.polySmall;
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
  
  handle = false;
  new CustomLayer(LAYER_ID, layer, legend);
}
