// Set theme dir
const themeDir = './';
const proxyUrl = 'http://localhost:80/';

module.exports = {
  cssnano: {
    preset: [
      'cssnano-preset-advanced',
      {
        discardComments: {
          removeAll: true,
        },
      },
    ],
  },
  size: {
    gzip: true,
    uncompressed: true,
    pretty: true,
    showFiles: true,
    showTotal: false,
  },
  rename: {
    min: {
      suffix: '.min',
    },
  },
  browsersync: {
    // Important! If src is wrong, styles will not inject to the browser
    src: [
      themeDir + '**/*.php',
      themeDir + 'css/**/*',
      themeDir + 'js/dev/**/*',
    ],
    opts: {
      logLevel: 'debug',
      injectChanges: true,
      proxy: proxyUrl,
      browser: 'Edge',
      open: true,
      notify: true,
      // Generate with: mkdir -p /var/www/certs && cd /var/www/certs && sudo mkcert localhost 192.168.x.xxx ::1
      https: {
        key: '/var/www/certs/localhost+2-key.pem',
        cert: '/var/www/certs/localhost+2.pem',
      },
    },
  },
  styles: {
    src: themeDir + 'sass/*.scss',
    development: themeDir + 'css/dev/',
    production: themeDir + 'css/prod/',
    watch: {
      development: themeDir + 'sass/**/*.scss',
      production: themeDir + 'css/dev/*.css',
    },
    stylelint: {
      src: themeDir + 'sass/**/*.scss',
      opts: {
        fix: false,
        reporters: [
          {
            formatter: 'string',
            console: true,
            failAfterError: false,
            debug: false,
          },
        ],
      },
    },
    opts: {
      development: {
        verbose: true,
        bundleExec: false,
        outputStyle: 'expanded',
        debugInfo: true,
        errLogToConsole: true,
        includePaths: [themeDir + 'node_modules/'],
        quietDeps: true,
      },
      production: {
        verbose: false,
        bundleExec: false,
        outputStyle: 'compressed',
        debugInfo: false,
        errLogToConsole: false,
        includePaths: [themeDir + 'node_modules/'],
        quietDeps: true,
      },
    },
  },
  js: {
    src: themeDir + 'js/src/*.js',
    watch: themeDir + 'js/src/**/*',
    production: themeDir + 'js/prod/',
    development: themeDir + 'js/dev/',
  },
  php: {
    watch: [
      themeDir + '*.php',
      themeDir + 'inc/**/*.php',
      themeDir + 'template-parts/**/*.php',
    ],
  },
  // phpcs: {
  //   src: [themeDir + '**/*.php', '!' + themeDir + 'node_modules/**/*'],
  //   opts: {
  //     bin: '/Users/damian/.composer/vendor/bin/phpcs',
  //     standard: themeDir + 'phpcs.xml',
  //     warningSeverity: 0,
  //   },
  // },
};
