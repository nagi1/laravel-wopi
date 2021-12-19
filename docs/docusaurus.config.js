// @ts-check
// Note: type annotations allow type checking and IDEs autocompletion

const lightCodeTheme = require("prism-react-renderer/themes/github");
const darkCodeTheme = require("prism-react-renderer/themes/dracula");

/** @type {import('@docusaurus/types').Config} */
const config = {
    title: "Laravel-wopi Docs",
    tagline: "Integrating office into your laravel apps",
    url: "https://nagi1.github.io",
    baseUrl: "/",
    onBrokenLinks: "throw",
    trailingSlash: false,
    onBrokenMarkdownLinks: "warn",
    favicon: "img/favicon.ico",
    organizationName: "nagi1", // Usually your GitHub org/user name.
    projectName: "laravel-wopi", // Usually your repo name.

    presets: [
        [
            "@docusaurus/preset-classic",
            /** @type {import('@docusaurus/preset-classic').Options} */
            ({
                docs: {
                    sidebarPath: require.resolve("./sidebars.js"),
                    editUrl: "https://github.com/nagi1/laravel-wopi",
                },

                theme: {
                    customCss: require.resolve("./src/css/custom.css"),
                },
            }),
        ],
    ],

    themeConfig:
        /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
        ({
            navbar: {
                title: "Laravel Wopi",
                // logo: {
                //     alt: "My Site Logo",
                //     src: "img/logo.svg",
                // },
                items: [
                    {
                        type: "doc",
                        docId: "introduction",
                        position: "left",
                        label: "Docs",
                    },
                    {
                        href: "https://github.com/nagi1/laravel-wopi",
                        label: "GitHub",
                        position: "right",
                    },
                ],
            },
            footer: {
                copyright: `Copyright © ${new Date().getFullYear()} Ahmed Nagi`,
            },
            prism: {
                additionalLanguages: ["php", "bash"],
                theme: lightCodeTheme,
                darkTheme: darkCodeTheme,
            },
        }),
};

module.exports = config;
