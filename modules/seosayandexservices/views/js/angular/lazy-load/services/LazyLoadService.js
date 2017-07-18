/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    SeoSA <885588@bk.ru>
 *  @copyright 2012-2017 SeoSA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Don't forget to prefix your containers with your own identifier
 * to avoid any conflicts with others containers.
 */
(function () {
    angular.module('sya.lazy-load')
        .provider('LazyLoader', LazyLoaderProvider);

    LazyLoaderProvider.$inject = ['$controllerProvider', '$provide', '$compileProvider', '$filterProvider'];
    function LazyLoaderProvider ($controllerProvider, $provide, $compileProvider, $filterProvider) {
        var modules = {},
            providers = {
                $controllerProvider: $controllerProvider,
                $compileProvider: $compileProvider,
                $filterProvider: $filterProvider,
                $provide: $provide // other things
            };

        var init = function (element) {
            var elements = [element],
                appElement,
                module,
                names = ['ng:app', 'ng-app', 'x-ng-app', 'data-ng-app'],
                NG_APP_CLASS_REGEXP = /\sng[:\-]app(:\s*([\w\d_]+);?)?\s/;

            function append(elm) {
                elm && elements.push(elm);
            }

            angular.forEach(names, function (name) {
                names[name] = true;
                name = name.replace(':', '\\:');
                angular.forEach(element.find('.' + name), append);
                angular.forEach(element.find('.' + name + '\\:'), append);
                angular.forEach(element.find('[' + name + ']'), append);
            });

            function push_module_to_cache(module)
            {
                module_cache.push(module)
                angular.forEach(get_module_requires(module), push_module_to_cache);
            }

            angular.forEach(elements, function (elm) {
                var className = ' ' + element.get(0).className + ' ';
                var match = NG_APP_CLASS_REGEXP.exec(className);


                if (match) {
                    module = (match[2] || '').replace(/\s+/g, ',');
                    push_module_to_cache(module);
                } else
                    angular.forEach(names, function (name) {
                        var value = $(elm).attr(name)

                        if (value) {
                            push_module_to_cache(value)
                        }
                    });
            });

            init = angular.noop;
        };

        function module_exists(name)
        {
            try {
                angular.module(name);
            } catch (e) {
                if (/No module/.test(e)) {
                    return false;
                }
            }
            return true;
        }

        function get_module_requires(module) {
            if (angular.isString(module))
                module = angular.module(module);
            var requires = [];

            angular.forEach(module.requires, function (requireModule) {
                if (requireModule !== 'ng')
                    requires.push(requireModule);
            });

            return requires;
        }

        function register($injector, providers, registerModules) {
            var i, ii, k, invokeQueue, moduleName, moduleFn, invokeArgs, provider;
            if (registerModules) {
                var runBlocks = [];
                for (k = registerModules.length-1; k >= 0; k--) {
                    moduleName = registerModules[k];
                    if (module_cache.indexOf(moduleName) > -1)
                        continue;
                    moduleFn = angular.module(moduleName);
                    runBlocks = runBlocks.concat(moduleFn._runBlocks);
                    try {
                        for (invokeQueue = moduleFn._invokeQueue, i = 0, ii = invokeQueue.length; i < ii; i++) {
                            invokeArgs = invokeQueue[i];

                            if (providers.hasOwnProperty(invokeArgs[0])) {
                                provider = providers[invokeArgs[0]];
                            } else {
                                return $log.error("unsupported provider " + invokeArgs[0]);
                            }
                            provider[invokeArgs[1]].apply(provider, invokeArgs[2]);
                        }
                    } catch (e) {
                        if (e.message) {
                            e.message += ' from ' + moduleName;
                        }
                        $log.error(e.message);
                        throw e;
                    }
                    registerModules.pop();
                }
                angular.forEach(runBlocks, function(fn) {
                    $injector.invoke(fn);
                });
            }
            return null;
        }


        var module_cache = [];
        module_cache.push = function (value) {
            if (this.indexOf(value) == -1) {
                Array.prototype.push.apply(this, arguments);
            }
        };

        LazyLoaderFactory.$inject = ['scriptCache', '$log', '$injector', '$q'];
        function LazyLoaderFactory(scriptCache, $log, $injector, $q) {

            function load_script(url)
            {
                var deferred = $q.defer();

                var scriptId = 'script:' + url,
                    scriptElement;

                if (!scriptCache.get(scriptId))
                {
                    scriptElement = $document[0].createElement('script');
                    scriptElement.src = url;
                    scriptElement.onload = deferred.resolve;
                    scriptElement.onerror = function () {
                        $log.error('Error loading "' + url + '"');
                        scriptCache.remove(scriptId);
                    };
                    $document[0].documentElement.appendChild(scriptElement);
                    scriptCache.put(scriptId, 1);
                }
                else
                    deferred.resolve();

                return deferred.promise
            }

            function load_dependencies(moduleName)
            {
                var deferred = $q.defer();
                var loaded_modules = [];
                loaded_modules.push = function (value) {
                    if (this.indexOf(value) == -1) {
                        Array.prototype.push.apply(this, arguments);
                    }
                };

                if (moduleName !== 'ng')
                {
                    var loaded_module = angular.module(moduleName);
                    var requires = get_module_requires(loaded_module);

                    function on_module_load(moduleLoaded) {
                        if (moduleLoaded) {

                            loaded_modules.push(moduleLoaded)
                            var index = requires.indexOf(moduleLoaded);
                            if (index > -1) {
                                requires.splice(index, 1);
                            }
                        }

                        if (requires.length === 0)
                        {
                            loaded_modules.push(moduleName)
                            deferred.resolve(loaded_modules);
                        }
                    }

                    angular.forEach(angular.copy(requires), function (required_module) {
                        if (module_exists(required_module)) {
                            return on_module_load(required_module);
                        }

                        LazyLoader.load(required_module).then(function() {
                            on_module_load(required_module);
                        })
                    });

                    if (requires.length == 0)
                    {
                        loaded_modules.push(moduleName)
                        deferred.resolve(loaded_modules);
                    }
                }
                else
                    deferred.resolve(loaded_modules);

                return deferred.promise;
            }


            var LazyLoader = {};

            LazyLoader.getConfig = function (name) {
                return modules[name] || {};
            };

            LazyLoader.load = function (name)
            {
                init(angular.element(window.document));

                var config = LazyLoader.getConfig(name);

                var on_dependencies_loaded = function (modules) {
                    return register($injector, providers, modules);
                };

                var on_scripts_loaded = function () {
                    return load_dependencies(name).then(on_dependencies_loaded);
                };

                if (config.script)
                    return load_script(config.script).then(on_scripts_loaded);
                else
                    return on_scripts_loaded();
            };

            return LazyLoader;
        }

        this.$get = LazyLoaderFactory;

        this.config = function (config) {
            if (angular.isArray(config)) {
                angular.forEach(config, function (moduleConfig) {
                    modules[moduleConfig.name] = moduleConfig;
                });
            } else {
                modules[config.name] = config;
            }
        };
    }
})();