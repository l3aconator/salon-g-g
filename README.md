# Studio

A WordPress theme for Studio.

This theme utilizes repositories, managers, services, and models for an [object-oriented approach](#-object-oriented-approach) to organizing your WordPress data.

Note that this repository is _just_ for your WordPress theme. The WordPress installation should live elsewhere.

## Table of Contents

- [🎁 What's in the Box](#-whats-in-the-box)
- [💻 System Requirements](#-system-requirements)
- [🛠 Installation](#-installation)
- [🏃‍ Development Workflow](#-development-workflow)
  - [Common wp-cli commands](#common-wp-cli-commands)
- [🔄 Object-Oriented Approach](#-object-oriented-approach)
  - [Managers](#managers)
  - [Models](#models)
  - [Repositories](#repositories)
  - [Services](#services)
- [📰 Gutenberg](#-gutenberg)
  - [Creating Custom ACF Blocks](#creating-custom-acf-blocks)
  - [Included Custom Blocks](#included-custom-blocks)

## 🎁 What's in the Box

- Full [Timber](https://www.upstatement.com/timber/) integration.
- Built-in support for [Ups Dock](https://github.com/Upstatement/ups-dock), so you can get a full WordPress site up a running with a few commands.
- Easy documentation creation with [Flatdoc](http://ricostacruz.com/flatdoc/).
- Code bundling with [Webpack](https://webpack.js.org/), including:
  - [BrowserSync](https://www.npmjs.com/package/browser-sync-webpack-plugin)
  - [Autoprefixer](https://github.com/postcss/autoprefixer)
  - [CSS Extraction](https://www.npmjs.com/package/mini-css-extract-plugin)
  - [Environment Variable Injection](https://www.npmjs.com/package/dotenv-webpack)
- Plugin management provided by [Composer](https://getcomposer.org/).
- Some useful PHP libraries:
  - [phpdotenv](https://github.com/vlucas/phpdotenv)
  - [carbon](https://carbon.nesbot.com/)
- Linting and testing:
  - JS, CSS, and PHP linting thanks to [Prettier](https://github.com/prettier/prettier), [ESLint](https://eslint.org/), and [phpcs](https://github.com/squizlabs/PHP_CodeSniffer)
  - Accessibility testing with [pa11y](https://github.com/pa11y/pa11y)
  - Bundle size limiting with [bundlesize](https://github.com/siddharthkp/bundlesize)
  - [Husky](https://github.com/typicode/husky) to automatically run these lints and tests!
- CI setup with [GitHub Actions](https://help.github.com/en/actions).

## 💻 System Requirements

Before you can start on your theme, you first need a way to run a LAMP/LEMP (Linux, Apache/nginx, MySQL, PHP) stack on your machine.

We recommend [Ups Dock](https://github.com/upstatement/ups-dock), a docker setup that will help with web projects. To install it follow these steps:

1. Install [Docker for Mac](https://www.docker.com/docker-mac)

2. Install [Ups Dock](https://github.com/upstatement/ups-dock) by following the installation steps in the [README](https://github.com/upstatement/ups-dock#installation)

## 🛠 Installation

1. Ensure [NVM](https://github.com/creationix/nvm) and [NPM](https://www.npmjs.com/) are installed globally.

2. Run `nvm install` to ensure you're using the correct version of Node.

If you're _not_ using Ups Dock, you can stop here! And use your LAMP setup of choice. Otherwise...

3. Duplicate the contents of `.env.sample` into a new `.env` file

4. Run the install command

   ```shell
   ./bin/install
   ```

5. Once the installation script has finished, run the start command

   ```shell
   ./bin/start
   ```

   Now you should be able to access your WordPress site on [`ups.dock`](http://ups.dock)!

   The default credentials for WP admin are `admin` / `password` (configurable via `docker-compose.yml`)

## 🏃‍ Development Workflow

1. Run `nvm use` to ensure you're using the correct version of Node

2. Run the start command to start the container and webpack server

   ```shell
   ./bin/start
   ```

   Not using Ups Dock? Run `npm run watch` instead

3. Visit the localhost URL in your browser

   By default this is https://localhost:3000/, which proxies your project's Ups Dock URL (i.e. https://salon.ups.dock)

4. Access the WP Admin Dashboard at `/wp-admin` (i.e. https://salon.ups.dock/wp-admin)

To shut down the container and development server, type `Ctrl+C`

### Common `wp-cli` commands

If you've installed this theme using Ups Dock, you can run `wp-cli` by typing `./bin/wp [command]`.

Start the Docker containers with `./bin/start` and then run any of the following commands in a separate shell:

```shell
./bin/wp [command]
```

To export the database, use the following command:

```shell
./bin/wp db export - > docker/conf/mysql/init.sql
```

To export the database and gzip it, use the following command:

```shell
./bin/wp db export - | gzip -3 > docker/conf/mysql/init.sql.gz
```

To SSH into the WordPress container, use the following command:

```shell
docker-compose exec wordpress /bin/bash
```

## 🔄 Object-Oriented Approach

This theme utilizes repositories, managers, services and models for a very object-oriented approach to organizing your WordPress data.

### Managers

Managers do things like:

- set up your theme (register option pages, hide dashboard widgets, enqueue JS and CSS, etc.)
- create custom post types and taxonomies
- set up basic WordPress defaults

### Models

Models hold and extend your data.

Have a press release post type that needs a bunch of extra functions? Create a class for them (extending `Timber\Post`) and put your logic there so you can keep your Twig clean!

### Repositories

Repositories are a good place to put query related logic.

It could be used in situations like the following:

> Let get me all the posts from September in the hot dog category!

### Services

Services are for more low-lying functions, like routing.

## 📰 Gutenberg

This theme has built-in support for easily creating custom Gutenberg blocks with the help of Advanced Custom Fields. Note that the pro version of ACF is required for this.

### Creating Custom ACF Blocks

1. Create a new ACF block class file in `/src/Blocks`.

2. Create a new twig file in your block directory to render the block.

3. Invoke the ACF block class in `/src/Blocks/Blocks.php`.

4. Add your new Gutenberg block to the array returned in the `allowBlocks` function in `/src/Managers/GutenbergManager.php`.

   ```php
    public function allowBlocks($allowed_blocks)
    {
        return array(
            ...
            'acf/acf-block',
            ...
        );
    }
   ```

Read more about [creating Gutenberg blocks using ACF](https://www.advancedcustomfields.com/resources/blocks/)

### Included Custom Blocks

The following custom blocks are included in the theme:
