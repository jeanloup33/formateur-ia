# Formateur IA – Frontend

Ce projet contient la landing page “IA Formations Bordeaux” optimisée pour la performance (Tailwind compilé localement, CSS personnalisée minifiée) et un mode sombre dynamique.

## Prérequis

- Node.js ≥ 18 (Node 22 est utilisé lors du développement)
- npm ≥ 9
- Python 3 (utilisé pour la minification de `assets/css/main.css`)
- Un navigateur Chromium/Chrome disponible localement pour l’audit Lighthouse

## Installation

```bash
npm install
```

Les scripts suivants sont ensuite disponibles :

- `npm run build:tailwind` : compile `src/tailwind.css` vers `assets/css/tailwind.generated.css`.
- `npm run build:css` : exécute la compilation Tailwind puis minifie `assets/css/main.css` (génère aussi `assets/css/main.min.css`).
- `npm run audit:lighthouse` : lance un audit Lighthouse automatisé (voir ci-dessous).

## Audit Lighthouse local

Le script `npm run audit:lighthouse` :

1. Démarre un serveur statique (http-server) sur `http://127.0.0.1:4173`.
2. Exécute Lighthouse en mode headless (`lighthouse --output html,json`).
3. Produit deux fichiers dans `./reports/` :
   - `lighthouse-report.html`
   - `lighthouse-report.json`

> **Remarque** : Lighthouse nécessite Chrome ou Chromium installé sur la machine, accessible via la variable d’environnement `CHROME_PATH` si le binaire n’est pas détecté automatiquement.

## Pré-commit

Le hook Husky `pre-commit` lance `npm run build:css`. Le commit est bloqué si la construction échoue (erreur Tailwind, Python absent, etc.). Pensez à installer les dépendances avant d’activer le hook :

```bash
npm install
npm run build:css  # première compilation pour générer les feuilles
```

## Structure

```
assets/
  css/
    main.css          # source CSS custom lisible
    main.min.css      # version minifiée (générée)
    tailwind.generated.css  # Tailwind compilé (généré)
scripts/
  minify_css.py       # minification maison
  run_lighthouse.js   # wrapper Lighthouse + http-server
src/
  tailwind.css        # directives Tailwind
index.html
```

## Commandes utiles

- `python3 scripts/minify_css.py` : minifie manuellement la CSS personnalisée.
- `npm run build:css` : utilisé dans le hook pre-commit, peut être lancé à tout moment pour régénérer les styles.
