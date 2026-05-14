# UI/UX Spec — InterviewPrep

## Vision Design

**Direction** : *Utilitarian Dark* — outil de travail sérieux, dense et lisible.
Pas d'animations superflues. Tout l'espace au contenu. Chaque élément gagne sa place.

**Palette**
```
--bg-base:      #0f1117   /* fond global */
--bg-surface:   #1a1d27   /* cards, panels */
--bg-elevated:  #242736   /* hover states, inputs */
--border:       #2e3245   /* séparateurs */
--accent:       #6366f1   /* indigo — CTA principal */
--accent-soft:  #4f46e5
--text-primary: #f1f3f9
--text-muted:   #8b90a7
--text-faint:   #4a4f6a

/* Statuts */
--status-review:   #ef4444   /* rouge  — À revoir */
--status-progress: #f59e0b   /* ambre  — En cours */
--status-mastered: #22c55e   /* vert   — Maîtrisé */

/* Niveaux */
--level-junior:  #38bdf8   /* sky    */
--level-mid:     #a78bfa   /* violet */
--level-senior:  #fb7185   /* rose   */
```

**Typographie**
```css
font-family: 'JetBrains Mono', monospace;   /* titres, labels, badges */
font-family: 'Inter', sans-serif;            /* corps de texte, explications */
```
> Importer depuis Google Fonts : `JetBrains Mono` (400, 600) + `Inter` (400, 500)

**Principes**
- Densité maximale sans surcharge : peu de whitespace superflu
- Badges toujours monospace — codes visuels cohérents
- Pas de bordures arrondies excessives : `border-radius: 6px` max
- Accent indigo uniquement sur les actions primaires

---

## Layout Global

### Structure principale
```
┌─────────────────────────────────────────────────────────┐
│  NAVBAR  [Logo]  [Domains]  [Dashboard]  [...] [Logout] │
├─────────────────────────────────────────────────────────┤
│                                                         │
│   BREADCRUMB  Domains > PHP OOP > Eloquent N+1          │
│                                                         │
│   PAGE CONTENT                                          │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Navbar
```html
<!-- Hauteur fixe : h-14 -->
<!-- bg-bg-surface border-b border-border -->
<nav>
  <span class="logo">InterviewPrep</span>        <!-- JetBrains Mono, accent color -->
  <a href="/domains">Domaines</a>
  <a href="/dashboard">Dashboard</a>
  <form action="/logout">Déconnexion</form>
</nav>
```

---

## Pages et Composants

---

### 1. Auth — Login / Register

**Layout** : Centré verticalement, card unique, fond bg-base avec bruit de texture subtil.

```
┌──────────────────────────────┐
│                              │
│   InterviewPrep              │
│   Prépare ton entretien.     │
│                              │
│   ┌────────────────────┐     │
│   │ Email              │     │
│   └────────────────────┘     │
│   ┌────────────────────┐     │
│   │ Password           │     │
│   └────────────────────┘     │
│                              │
│   [  Se connecter  ]         │
│                              │
│   Pas de compte ? S'inscrire │
└──────────────────────────────┘
```

**Détails**
- Card : `bg-surface`, `border border-border`, `rounded-md`, `p-8`, `w-full max-w-md`
- Input : `bg-elevated border border-border rounded-md px-4 py-2.5 text-sm font-mono focus:border-accent`
- Bouton primaire : `bg-accent hover:bg-accent-soft text-white font-mono text-sm px-6 py-2.5 rounded-md w-full`
- Erreurs de validation : `text-status-review text-xs mt-1` sous chaque champ
- Pas d'image de fond — grain CSS uniquement :
  ```css
  body::before { content:''; position:fixed; inset:0;
    background-image: url("data:image/svg+xml,..."); opacity:.03; pointer-events:none; }
  ```

---

### 2. Dashboard

**Layout** : Header stats en grid 4 colonnes, puis highlights, puis liste domaines.

```
┌─────────────────────────────────────────────────────────┐
│  Bonjour, Yassine.                    [+ Nouveau domaine]│
├──────────┬──────────┬──────────┬──────────────────────── │
│  Total   │ À revoir │ En cours │     Maîtrisés           │
│  24      │ 10  🔴   │  8  🟡   │     6  🟢               │
│          │          │          │  ████░░░░  25%          │
├──────────┴──────────┴──────────┴────────────────────────┤
│  🏆 Mieux maîtrisé : PHP OOP (80%)                       │
│  ⚠️  Priorité : MySQL (8 à revoir)                        │
├─────────────────────────────────────────────────────────┤
│  DOMAINES                                                │
│  ┌───────────────────────────────────────────────────┐  │
│  │ [●blue] Laravel ORM    12 concepts                │  │
│  │ ████████████░░░░░░  67%  [8 maîtrisés / 12]      │  │
│  └───────────────────────────────────────────────────┘  │
│  ┌───────────────────────────────────────────────────┐  │
│  │ [●green] PHP OOP       5 concepts                 │  │
│  │ ████████████████░░  80%  [4 maîtrisés / 5]       │  │
│  └───────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

**Composant stat card**
```html
<div class="bg-surface border border-border rounded-md p-5">
  <p class="text-muted text-xs font-mono uppercase tracking-widest">À revoir</p>
  <p class="text-4xl font-mono font-semibold text-status-review mt-1">10</p>
</div>
```

**Barre de progression**
```html
<div class="w-full bg-elevated rounded-full h-1.5 mt-2">
  <div class="bg-accent h-1.5 rounded-full transition-all"
       style="width: {{ $percent }}%"></div>
</div>
<p class="text-muted text-xs font-mono mt-1">{{ $mastered }}/{{ $total }} maîtrisés</p>
```

---

### 3. Liste des Domaines

**Layout** : Grid 3 colonnes sur desktop, 1 sur mobile. Bouton "+" flottant en haut à droite.

```
Mes Domaines                                   [+ Créer un domaine]

┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│ [●blue]          │ │ [●green]         │ │ [●purple]        │
│ Laravel ORM     │ │ PHP OOP         │ │ MySQL           │
│                 │ │                 │ │                 │
│ 12 concepts     │ │ 5 concepts      │ │ 8 concepts      │
│ 8 maîtrisés     │ │ 4 maîtrisés     │ │ 1 maîtrisé      │
│ ██████████░░    │ │ ████████████░░  │ │ ██░░░░░░░░░░    │
│                 │ │                 │ │                 │
│ [Voir] [✏] [🗑] │ │ [Voir] [✏] [🗑] │ │ [Voir] [✏] [🗑] │
└─────────────────┘ └─────────────────┘ └─────────────────┘
```

**Card domaine**
```html
<div class="bg-surface border border-border rounded-md p-5 hover:border-accent/50 transition-colors">
  <!-- Header -->
  <div class="flex items-center gap-2 mb-3">
    <span class="w-3 h-3 rounded-full bg-{{ $domain->color }}-500"></span>
    <h3 class="font-mono font-semibold text-primary">{{ $domain->name }}</h3>
  </div>
  <!-- Stats -->
  <p class="text-muted text-sm">
    <span class="text-primary font-mono">{{ $domain->concepts_count }}</span> concepts ·
    <span class="text-status-mastered font-mono">{{ $domain->mastered_count }}</span> maîtrisés
  </p>
  <!-- Barre -->
  ...
  <!-- Actions -->
  <div class="flex gap-2 mt-4 pt-4 border-t border-border">
    <a href="..." class="btn-primary flex-1 text-center text-sm">Voir</a>
    <a href="..." class="btn-ghost text-sm">✏</a>
    <form method="POST" ...><button class="btn-ghost text-sm text-status-review">🗑</button></form>
  </div>
</div>
```

**Formulaire Create/Edit Domaine**
```
┌──────────────────────────────────────┐
│ Nom du domaine                       │
│ ┌──────────────────────────────────┐ │
│ │ Laravel ORM                      │ │
│ └──────────────────────────────────┘ │
│                                      │
│ Couleur du badge                     │
│  ⬤  ⬤  ⬤  ⬤  ⬤  ⬤  ⬤  ⬤          │
│ blue grn red prp org ylw pnk gry    │
│                                      │
│           [Annuler]  [Enregistrer]   │
└──────────────────────────────────────┘
```

Sélecteur couleur : radio buttons stylisés en cercles colorés, `checked:ring-2 ring-white ring-offset-2 ring-offset-surface`.

---

### 4. Liste des Concepts d'un Domaine

**Layout** : Full width, tableau-like list. Filtres en haut.

```
← Domaines   /   Laravel ORM                      [+ Nouveau concept]

Filtrer :  [Tous ▾]  [Junior|Mid|Senior]           12 concepts

┌──────────────────────────────────────────────────────────────────────┐
│ TITRE                      NIVEAU        STATUT           ACTIONS    │
├──────────────────────────────────────────────────────────────────────┤
│ Eloquent N+1 Problem       [Mid]         [En cours  ▾]   [👁] [✏][🗑]│
│ Service Container          [Senior]      [À revoir  ▾]   [👁] [✏][🗑]│
│ Query Scopes               [Junior]      [Maîtrisé  ▾]   [👁] [✏][🗑]│
└──────────────────────────────────────────────────────────────────────┘
```

**Row concept**
```html
<tr class="border-b border-border hover:bg-elevated transition-colors">
  <td class="py-3 px-4">
    <a href="{{ route('concepts.show', ...) }}" class="text-primary font-medium hover:text-accent">
      {{ $concept->title }}
    </a>
  </td>
  <td class="py-3 px-4">
    <span class="badge badge-{{ $concept->difficulty }}">{{ $concept->difficultyLabel }}</span>
  </td>
  <td class="py-3 px-4">
    <!-- Select inline pour changement rapide US9 -->
    <form method="POST" action="{{ route('concepts.updateStatus', $concept) }}">
      @method('PATCH') @csrf
      <select name="status" onchange="this.form.submit()"
              class="bg-elevated border border-border text-sm font-mono rounded px-2 py-1
                     text-{{ $concept->status === 'mastered' ? 'status-mastered' :
                             ($concept->status === 'in_progress' ? 'status-progress' : 'status-review') }}">
        <option value="to_review"   @selected($concept->status==='to_review')>À revoir</option>
        <option value="in_progress" @selected($concept->status==='in_progress')>En cours</option>
        <option value="mastered"    @selected($concept->status==='mastered')>Maîtrisé</option>
      </select>
    </form>
  </td>
  <td class="py-3 px-4">
    <div class="flex gap-2">
      <a href="show" class="icon-btn">👁</a>
      <a href="edit" class="icon-btn">✏</a>
      <form method="POST" action="destroy">@method('DELETE')@csrf
        <button class="icon-btn text-status-review">🗑</button>
      </form>
    </div>
  </td>
</tr>
```

**Badges CSS**
```css
.badge {
  @apply font-mono text-xs px-2 py-0.5 rounded-sm font-semibold uppercase tracking-wide;
}
.badge-junior  { @apply bg-sky-900/50 text-sky-300; }
.badge-mid     { @apply bg-violet-900/50 text-violet-300; }
.badge-senior  { @apply bg-rose-900/50 text-rose-300; }
```

**Filtres**
```html
<!-- Filtre statut -->
<div class="flex gap-2">
  @foreach(['', 'to_review', 'in_progress', 'mastered'] as $s)
    <a href="?status={{ $s }}"
       class="text-xs font-mono px-3 py-1.5 rounded border
              {{ request('status') === $s ? 'bg-accent border-accent text-white' : 'border-border text-muted hover:border-accent/50' }}">
      {{ $s ?: 'Tous' }}
    </a>
  @endforeach
</div>
```

---

### 5. Détail d'un Concept

**Layout** : 2 colonnes sur desktop — gauche : info concept + explication. Droite : questions AI.

```
← Laravel ORM   /   Eloquent N+1 Problem

┌──────────────────────────────────┬─────────────────────────────────┐
│  [Mid]  [En cours]               │  QUESTIONS D'ENTRETIEN          │
│                                  │                                  │
│  Eloquent N+1 Problem            │  [⚡ Générer 5 questions]        │
│  ─────────────────────────────   │  ─────────────────────────────  │
│                                  │                                  │
│  EXPLICATION                     │  Génération du 12/05 à 14h30    │
│  Lorem ipsum... texte long...    │  [Supprimer]                    │
│                                  │  1. Qu'est-ce que le N+1 ?      │
│                                  │  2. Comment le détecter ?       │
│  [✏ Modifier]  [🗑 Supprimer]    │  3. Quelle solution Eloquent ?  │
│                                  │  4. ...                         │
│                                  │  5. ...                         │
│                                  │                                  │
│                                  │  Génération du 11/05 à 09h15   │
│                                  │  [Supprimer]                    │
│                                  │  1. ...                         │
└──────────────────────────────────┴─────────────────────────────────┘
```

**Bouton Générer**
```html
<form method="POST" action="{{ route('questions.store', $concept) }}">
  @csrf
  <button type="submit"
          class="flex items-center gap-2 bg-accent hover:bg-accent-soft
                 text-white font-mono text-sm px-5 py-2.5 rounded-md
                 transition-colors w-full justify-center">
    <span>⚡</span>
    Générer 5 questions d'entretien
  </button>
</form>
```

**Card génération**
```html
<div class="bg-elevated border border-border rounded-md p-4 mb-4">
  <div class="flex justify-between items-center mb-3">
    <span class="text-muted text-xs font-mono">
      {{ $gen->created_at->format('d/m/Y à H\hi') }}
    </span>
    <form method="POST" action="{{ route('questions.destroy', $gen) }}">
      @method('DELETE') @csrf
      <button class="text-muted hover:text-status-review text-xs font-mono">Supprimer</button>
    </form>
  </div>
  <ol class="space-y-2">
    @foreach($gen->questions as $i => $q)
      <li class="flex gap-3 text-sm">
        <span class="text-accent font-mono font-semibold w-4 shrink-0">{{ $i+1 }}.</span>
        <span class="text-primary/90">{{ $q }}</span>
      </li>
    @endforeach
  </ol>
</div>
```

---

### 6. Formulaires Create/Edit Concept

```
┌─────────────────────────────────────────────────────┐
│  Titre du concept                                   │
│  ┌───────────────────────────────────────────────┐  │
│  │ Eloquent N+1 Problem                          │  │
│  └───────────────────────────────────────────────┘  │
│                                                     │
│  Explication                                        │
│  ┌───────────────────────────────────────────────┐  │
│  │ Le problème N+1 survient quand Eloquent        │  │
│  │ exécute 1 requête pour la liste + N requêtes  │  │
│  │ pour chaque relation...                       │  │
│  │                                               │  │
│  │                                               │  │
│  └───────────────────────────────────────────────┘  │
│                                                     │
│  Niveau de difficulté                               │
│  ( ) Junior   (●) Mid   ( ) Senior                  │
│                                                     │
│                    [Annuler]  [Enregistrer]          │
└─────────────────────────────────────────────────────┘
```

- Textarea `explanation` : `min-h-[200px] resize-y font-sans text-sm`
- Radios difficulté : stylisés comme boutons toggle `border border-border rounded px-4 py-2 font-mono text-sm checked:bg-accent`

---

## Composants réutilisables (Blade components)

### `<x-badge-status :status="$concept->status" :label="$concept->statusLabel" />`
```html
@props(['status', 'label'])
<span class="badge
  {{ $status === 'mastered' ? 'bg-green-900/50 text-green-300' :
     ($status === 'in_progress' ? 'bg-amber-900/50 text-amber-300' : 'bg-red-900/50 text-red-300') }}">
  {{ $label }}
</span>
```

### `<x-badge-difficulty :level="$concept->difficulty" :label="$concept->difficultyLabel" />`
```html
@props(['level', 'label'])
<span class="badge
  {{ $level === 'junior' ? 'bg-sky-900/50 text-sky-300' :
     ($level === 'mid' ? 'bg-violet-900/50 text-violet-300' : 'bg-rose-900/50 text-rose-300') }}">
  {{ $label }}
</span>
```

### `<x-flash-message />`
```html
@if(session('success'))
  <div class="bg-green-900/30 border border-green-700 text-green-300 text-sm font-mono
              px-4 py-3 rounded-md mb-4">
    {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="bg-red-900/30 border border-red-700 text-red-300 text-sm font-mono
              px-4 py-3 rounded-md mb-4">
    {{ session('error') }}
  </div>
@endif
```

---

## Boutons — Classes utilitaires

```css
/* À mettre dans resources/css/app.css ou dans un @layer components Tailwind */

.btn-primary {
  @apply bg-indigo-600 hover:bg-indigo-700 text-white font-mono text-sm
         px-5 py-2.5 rounded-md transition-colors;
}

.btn-ghost {
  @apply bg-transparent hover:bg-elevated border border-border text-muted
         hover:text-primary font-mono text-sm px-4 py-2 rounded-md transition-colors;
}

.icon-btn {
  @apply p-1.5 rounded hover:bg-elevated text-muted hover:text-primary transition-colors;
}
```

---

## Layout Blade de base — `layouts/app.blade.php`

```html
<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>InterviewPrep — @yield('title')</title>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Inter:wght@400;500&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#0f1117] text-[#f1f3f9] min-h-screen font-sans">

  <!-- Navbar -->
  <nav class="h-14 bg-[#1a1d27] border-b border-[#2e3245] flex items-center px-6 gap-6">
    <a href="/dashboard" class="font-mono font-semibold text-indigo-400 text-sm tracking-wide">
      InterviewPrep
    </a>
    <a href="{{ route('domains.index') }}" class="text-sm text-[#8b90a7] hover:text-white transition-colors">
      Domaines
    </a>
    <a href="{{ route('dashboard') }}" class="text-sm text-[#8b90a7] hover:text-white transition-colors">
      Dashboard
    </a>
    <div class="ml-auto">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="text-sm text-[#8b90a7] hover:text-white font-mono transition-colors">
          Déconnexion
        </button>
      </form>
    </div>
  </nav>

  <!-- Contenu -->
  <main class="max-w-6xl mx-auto px-6 py-8">
    <x-flash-message />
    @yield('content')
  </main>

</body>
</html>
```

---

## tailwind.config.js — Extensions

```js
module.exports = {
  content: ['./resources/**/*.blade.php'],
  theme: {
    extend: {
      colors: {
        surface:  '#1a1d27',
        elevated: '#242736',
        border:   '#2e3245',
        accent:   '#6366f1',
      },
      fontFamily: {
        mono: ['"JetBrains Mono"', 'monospace'],
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
};
```

---

## Responsive — Points de rupture

| Breakpoint | Changement |
|-----------|------------|
| `sm` (640px) | Cards domaines : 1 col → 2 cols |
| `md` (768px) | Stats dashboard : 2 cols → 4 cols |
| `lg` (1024px) | Détail concept : stack → 2 cols |
| Mobile | Navbar : liens cachés sauf logo + logout |

---

## États vides (Empty States)

Chaque liste a son empty state pour guider l'utilisateur :

```html
<!-- Aucun domaine -->
<div class="text-center py-16">
  <p class="text-4xl mb-4">🗂️</p>
  <p class="font-mono text-muted mb-2">Aucun domaine pour l'instant.</p>
  <a href="{{ route('domains.create') }}" class="btn-primary">Créer mon premier domaine</a>
</div>

<!-- Aucun concept -->
<div class="text-center py-16">
  <p class="text-4xl mb-4">✍️</p>
  <p class="font-mono text-muted mb-2">Ce domaine n'a pas encore de concepts.</p>
  <a href="{{ route('domains.concepts.create', $domain) }}" class="btn-primary">Ajouter un concept</a>
</div>

<!-- Aucune génération -->
<div class="text-center py-8 border border-dashed border-border rounded-md">
  <p class="text-muted font-mono text-sm">Aucune génération pour ce concept.</p>
  <p class="text-faint text-xs mt-1">Clique sur "Générer" pour démarrer.</p>
</div>
```
