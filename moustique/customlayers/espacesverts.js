// Styles catégorisés par type
const stylesByType = {
  "1. Parcs et squares": "#2ecc71", // vert vif
  "2. Acc. de voiries": "#27ae60", // vert forêt
  "3. Acc. de bât. et équipements publics": "#16a085", // vert foncé
  "4. Acc. d'habitations": "#1abc9c", // turquoise
  "5. Acc. de zones indus. et commerciales": "#8e44ad", // violet sombre
  "6. Espaces verts écoles et crèches": "#2c3e50", // gris bleu
  "7. Terrains de sport": "#7f8c8d", // gris neutre
  "8. Cimetières": "#a0522d", // brun
  "9. Espaces de nature": "#d2b48c", // beige (tan)
  "10. Bassins de rétention": "#deb887", // burlywood
  "11. Parking": "#cd853f", // peru
  "12. Place": "#556b2f", // vert olive
  "13. Placette": "#6b8e23", // olive drab
  "14. Placette de lotissement": "#bdb76b", // dark khaki
  "15. Jardins familiaux": "#f4a460", // sable
  "16. Boulodrome": "#daa520", // goldenrod
  "17. Abord terrain de sport": "#8b4513", // saddle brown
  "18. Etablissement horticole": "#a9a9a9", // gris foncé
  "19. Espaces verts autres": "#228b22", // green
};

function getStyleByType(feature) {
  const type = feature.get("ev_typologie");
  const fillColor = stylesByType[type] || "#bdc3c7";
  return new ol.style.Style({
    fill: new ol.style.Fill({ color: fillColor }),
    stroke: new ol.style.Stroke({ color: "#2c3e50", width: 1.5 }),
  });
}

let legendItems = Object.entries(stylesByType).map(([label, color]) => {
  return {
    label: label,
    geometry: "Polygon",
    styles: [
      new ol.style.Style({
        fill: new ol.style.Fill({ color: color }),
        stroke: new ol.style.Stroke({ color: "#2c3e50", width: 1.5 }),
      }),
    ],
  };
});

let layer = new ol.layer.Vector({
  source: new ol.source.Vector({
    url: "apps/omees/data/MMM_MMM_EspacesVerts.json",
    // url: "https://data.montpellier3m.fr/sites/default/files/ressources/MMM_MMM_EspacesVerts.json",
    format: new ol.format.GeoJSON(),
  }),
  style: getStyleByType,
});

new CustomLayer("espacesverts", layer, { items: legendItems });
