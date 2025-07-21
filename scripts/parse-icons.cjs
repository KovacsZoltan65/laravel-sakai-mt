// scripts/parse-icons.cjs
const fs = require('fs');
const path = require('path');

const inputPath = path.resolve(__dirname, '../node_modules/primeicons/primeicons.css');
const outputPath = path.resolve(__dirname, '../src/data/icons.js');

const cssContent = fs.readFileSync(inputPath, 'utf-8');

const matches = [...cssContent.matchAll(/\.pi-([a-z0-9-]+):before/g)];

const icons = matches.map((match) => {
  const name = match[1];
  return {
    label: name.replace(/-/g, ' '),
    value: `pi pi-${name}`,
  };
});

icons.sort((a, b) => a.label.localeCompare(b.label));

const output = `// Auto-generated from primeicons.css
export default ${JSON.stringify(icons, null, 2)};
`;

fs.writeFileSync(outputPath, output);

console.log(`✅ icons.js generated with ${icons.length} icons at: src/data/icons.js`);
