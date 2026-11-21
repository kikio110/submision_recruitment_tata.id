import { copyFileSync, mkdirSync } from 'node:fs';
mkdirSync('public/vendor', { recursive: true });
copyFileSync('node_modules/chart.js/dist/chart.umd.js', 'public/vendor/chart.umd.js');
