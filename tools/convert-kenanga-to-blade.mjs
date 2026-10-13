import fs from 'node:fs';
import path from 'node:path';

const file = process.argv[2];
let source = fs.readFileSync(file, 'utf8');
const metadata = { title: path.basename(file, '.html'), group: 'Komponen', layout: 'app' };
const metadataMatch = source.match(/^<!--page([^>]*)-->\s*/);
if (metadataMatch) {
  metadataMatch[1].replace(/([\w-]+)="([^"]*)"/g, (_, key, value) => (metadata[key] = value));
  source = source.slice(metadataMatch[0].length);
}

const attrMap = { desc: 'description', dtone: 'tone', dir: 'direction' };
source = source.replace(/\{\{>\s*([\w-]+)((?:\s+[\w-]+="[^"]*")*)\s*\}\}/g, (_, name, rawAttrs) => {
  const attrs = [];
  rawAttrs.replace(/([\w-]+)="([^"]*)"/g, (match, key, value) => {
    attrs.push([attrMap[key] ?? key, value]);
    return match;
  });
  const renderedAttrs = attrs.map(([key, value]) => ` ${key}="${value}"`).join('');
  if (name === 'card-start') return `<x-ui.card${renderedAttrs}>`;
  if (name === 'card-end') return '</x-ui.card>';
  return `<x-ui.${name}${renderedAttrs} />`;
});

const loops = ['users', 'orders', 'products', 'activity', 'sales'];
for (const loop of loops) {
  source = source.replaceAll(`{{#each ${loop}}}`, `@foreach (config('kenanga.demo.${loop}') as $item)`);
}
source = source.replaceAll('{{/each}}', '@endforeach');
source = source.replace(/\{\{this\.([\w.]+)\}\}/g, (_, key) => `{{ $item['${key}'] }}`);
source = source.replace(/name="\{\{ \$item\['([\w.]+)'\] \}\}"/g, (_, key) => `:name="$item['${key}']"`);

const routeMap = {
  'index.html': "{{ route('admin.dashboard') }}",
  'analytics.html': "{{ route('admin.analytics') }}",
  'settings.html': "{{ route('admin.settings') }}",
  'cards.html': "{{ route('showcase.cards') }}",
  'tables.html': "{{ route('showcase.tables') }}",
  'forms.html': "{{ route('showcase.forms') }}",
  'buttons.html': "{{ route('showcase.buttons') }}",
  'feedback.html': "{{ route('showcase.feedback') }}",
  'navigation.html': "{{ route('showcase.navigation') }}",
  'login.html': "{{ route('login') }}",
  '404.html': "{{ route('demo.404') }}",
};
for (const [html, route] of Object.entries(routeMap)) {
  source = source.replaceAll(`href="${html}"`, `href="${route}"`);
}

const layout = metadata.layout === 'blank' ? 'guest' : 'admin';
const group = layout === 'admin' ? ` group="${metadata.group}"` : '';
process.stdout.write(`<x-layouts.${layout} title="${metadata.title}"${group}>\n${source.trim()}\n</x-layouts.${layout}>\n`);
