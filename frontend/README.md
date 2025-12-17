# Mental Health Consultation - Frontend

Frontend application built with Nuxt 4, Vue 3, and TypeScript for the Mental Health Consultation Web App.

## Technology Stack

- **Framework**: Nuxt 4.2.2
- **UI Library**: Vue 3.5.25
- **Language**: TypeScript
- **Styling**: Tailwind CSS 4.x
- **Package Manager**: pnpm

## Architecture

### Key Features

- **Composable-based API layer**: All API interactions through composables (`useAuth`, `usePatients`, `useConsultations`, etc.)
- **Reusable components**: `FormError`, `LoadingSpinner`, `Toast`, `EmptyState`, `ConfirmModal`
- **Middleware**: Authentication and role-based access control
- **Error handling**: Centralized error handling with `useErrorHandler`
- **Form validation**: Field-specific validation error display
- **Search debouncing**: Optimized search with 300-500ms delay
- **Responsive design**: Mobile-friendly sidebar navigation

### Project Structure

- `composables/`: Shared logic for API calls and state management
- `components/`: Reusable Vue components
- `pages/`: File-based routing (Nuxt pages)
- `layouts/`: Page layouts (default layout with sidebar)
- `middleware/`: Route middleware (auth, role)
- `types/`: TypeScript type definitions
- `plugins/`: Nuxt plugins (auto-fetch user on init)

See [Implementation Guide](../docs/09-implementation-guide.md) for detailed architecture documentation.

## Setup

Make sure to install dependencies:

```bash
# npm
npm install

# pnpm
pnpm install

# yarn
yarn install

# bun
bun install
```

## Development Server

Start the development server on `http://localhost:3000`:

```bash
# npm
npm run dev

# pnpm
pnpm dev

# yarn
yarn dev

# bun
bun run dev
```

## Production

Build the application for production:

```bash
# npm
npm run build

# pnpm
pnpm build

# yarn
yarn build

# bun
bun run build
```

Locally preview production build:

```bash
# npm
npm run preview

# pnpm
pnpm preview

# yarn
yarn preview

# bun
bun run preview
```

Check out the [deployment documentation](https://nuxt.com/docs/getting-started/deployment) for more information.
