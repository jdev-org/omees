let albopictusStyle = new ol.style.Style({
  image: new ol.style.Circle({
    fill: new ol.style.Fill({
      color: 'rgba(255, 0, 0, 0.6)'
    }),
    stroke: new ol.style.Stroke({
      color: '#fff',
      width: 1
    }),
    radius: 6
  })
});

let legend = {
  items: [
    {
      label: "Observation",
      geometry: "Point",
      styles: [albopictusStyle]
    }
  ]
};

const source = new ol.source.Vector({
    url: "https://geodata.bac-a-sable.inrae.fr/geoserver/omees/ows?service=WFS&request=GetFeature&typeName=omees:albopictus_arbocarto_montpellier_weekly&outputFormat=application/json&srsName=EPSG:3857",
    format: new ol.format.GeoJSON()
  })

source.on('featuresloadend', function(event) {
  console.log("✅ Données chargées :", event.features);
});

let layer = new ol.layer.Vector({
  source: source,
  style: function(feature) {
    return albopictusStyle;
  }
});

new CustomLayer("albopictus", layer, legend);