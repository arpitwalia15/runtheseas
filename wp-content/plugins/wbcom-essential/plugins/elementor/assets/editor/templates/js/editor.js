(function ($) {

	'use strict';
	
	var WBcomEssentialelementorSectionsData = window.WBcomEssentialelementorSectionsData || {},
		WBcomEssentialelementorSectionsEditor,
		WBcomEssentialelementorSectionsEditorViews;

	WBcomEssentialelementorSectionsEditorViews = {

		ModalLayoutView: null,
		ModalHeaderView: null,
		ModalLoadingView: null,
		ModalBodyView: null,
		ModalErrorView: null,
		LibraryCollection: null,
		ModalCollectionView: null,
		ModalTabsCollection: null,
		ModalTabsCollectionView: null,
		FiltersCollectionView: null,
		FiltersItemView: null,
		ModalTabsItemView: null,
		ModalTemplateItemView: null,
		ModalInsertTemplateBehavior: null,
		ModalTemplateModel: null,
		CategoriesCollection: null,
		ModalHeaderLogo: null,
		TabModel: null,
		CategoryModel: null,
		TemplatesEmptyView: null,
		TemplateSearchCollectionView: null,

		init: function () {
			var self = this;

			self.ModalTemplateModel = Backbone.Model.extend({
				defaults: {
					template_id: 0,
					title: '',
					thumbnail: '',
					source: '',
					categories: []
				}
			});

			self.ModalHeaderView = Marionette.LayoutView.extend({

				id: 'wbcomessentialelementor-template-modal-header',
				template: '#tmpl-wbcomessentialelementor-template-modal-header',

				ui: {
					closeModal: '#wbcomessentialelementor-template-modal-header-close-modal'
				},

				events: {
					'click @ui.closeModal': 'onCloseModalClick'
				},

				regions: {
					headerLogo: '#wbcomessentialelementor-template-modal-header-logo-area',
					headerTabs: '#wbcomessentialelementor-template-modal-header-tabs',
					headerActions: '#wbcomessentialelementor-template-modal-header-actions'
				},

				onCloseModalClick: function () {
					WBcomEssentialelementorSectionsEditor.closeModal();
				}

			});

			self.TabModel = Backbone.Model.extend({
				defaults: {
					slug: '',
					title: ''
				}
			});

			self.LibraryCollection = Backbone.Collection.extend({
				model: self.ModalTemplateModel
			});

			self.ModalTabsCollection = Backbone.Collection.extend({
				model: self.TabModel
			});

			self.CategoryModel = Backbone.Model.extend({
				defaults: {
					slug: '',
					title: ''
				}
			});

			self.CategoriesCollection = Backbone.Collection.extend({
				model: self.CategoryModel
			});

			self.ModalHeaderLogo = Marionette.ItemView.extend({

				template: '#tmpl-wbcomessentialelementor-template-modal-header-logo',

				id: 'wbcomessentialelementor-template-modal-header-logo'

			});

			self.ModalBodyView = Marionette.LayoutView.extend({

				id: 'wbcomessentialelementor-template-library-content',

				className: function () {
					return 'library-tab-' + WBcomEssentialelementorSectionsEditor.getTab();
				},

				template: '#tmpl-wbcomessentialelementor-template-modal-content',

				regions: {
					contentTemplates: '.wbcomessentialelementor-templates-list',
					contentFilters: '.wbcomessentialelementor-filters-list',
					contentSearch: '#elementor-template-library-filter-text-wrapper',
				}

			});

			self.TemplatesEmptyView = Marionette.LayoutView.extend({

				id: 'wbcomessentialelementor-template-modal-empty',

				template: '#tmpl-wbcomessentialelementor-template-modal-empty',

				ui: {
					title: '.elementor-template-library-blank-title',
				},

				regions: {
					contentTemplates: '.wbcomessentialelementor-templates-list',
					contentFilters: '.wbcomessentialelementor-filters-list',
					contentSearch: '#elementor-template-library-filter-text-wrapper',
				}

			});

			self.ModalInsertTemplateBehavior = Marionette.Behavior.extend({
				ui: {
					insertButton: '.wbcomessentialelementor-template-insert'
				},

				events: {
					'click @ui.insertButton': 'onInsertButtonClick'
				},

				onInsertButtonClick: function () {

					var templateModel = this.view.model,
						options = {};

					WBcomEssentialelementorSectionsEditor.layout.showLoadingView();
					$.ajax({
						url: ajaxurl,
						type: 'post',
						dataType: 'json',
						data: {
							action: 'wbcom_essential_elementor_sections_inner_template',
							template: templateModel.attributes,
							tab: WBcomEssentialelementorSectionsEditor.getTab(),
							nonce: WBcomEssentialelementorSectionsData.nonce
						}
					});

					elementor.templates.requestTemplateContent(
						templateModel.get('source'),
						templateModel.get('template_id'),
						{
							data: {
								tab: WBcomEssentialelementorSectionsEditor.getTab(),
								page_settings: false
							},
							success: function (data) {

								console.log("%c Template Inserted Successfully!!", "color: #7a7a7a; background-color: #eee;");

								WBcomEssentialelementorSectionsEditor.closeModal();

								elementor.channels.data.trigger('template:before:insert', templateModel);

								if (null !== WBcomEssentialelementorSectionsEditor.atIndex) {
									options.at = WBcomEssentialelementorSectionsEditor.atIndex;
								}

								elementor.previewView.addChildModel(data.content, options);

								elementor.channels.data.trigger('template:after:insert', templateModel);

								WBcomEssentialelementorSectionsEditor.atIndex = null;
								jQuery('.elementor-button-success').removeClass('elementor-disabled');
							},
							error: function (err) {
								WBcomEssentialelementorSectionsEditor.closeModal();
							}
						}
					);
				}
			});

			self.FiltersItemView = Marionette.ItemView.extend({

				template: '#tmpl-wbcomessentialelementor-template-modal-filters-item',

				className: function () {
					return 'wbcomessentialelementor-template-filter-item';
				},

				ui: function () {
					return {
						filterLabels: '.wbcomessentialelementor-template-filter-label'
					};
				},

				events: function () {
					return {
						'click @ui.filterLabels': 'onFilterClick'
					};
				},

				onFilterClick: function (event) {

					var $clickedInput = jQuery(event.target);
					WBcomEssentialelementorSectionsEditor.setFilter('category', $clickedInput.val());
				}

			});

			self.TemplateSearchCollectionView = Marionette.CompositeView.extend({

				template: '#tmpl-wbcomessentialelementor-template-modal-search-item',
				id: 'wbcomessentialelementor-template-modal-search-item',

				ui: function () {
					return {
						textFilter: '#elementor-template-library-filter-text',
					};
				},

				events: function () {
					return {
						'input @ui.textFilter': 'onTextFilterInput',
					};
				},

				onTextFilterInput: function onTextFilterInput( childModel ) {
					
					var searchText = this.ui.textFilter.val();
					searchText = searchText.replace(/[<>]/g, '').trim(); //explicitly remove angle brackets to prevent XSS attacks.
					WBcomEssentialelementorSectionsEditor.setFilter('text', searchText);
				},

			});

			self.ModalTabsItemView = Marionette.ItemView.extend({

				template: '#tmpl-wbcomessentialelementor-template-modal-tabs-item',

				className: function () {
					return 'elementor-template-library-menu-item';
				},

				ui: function () {
					return {
						tabsLabels: 'label',
						tabsInput: 'input'
					};
				},

				events: function () {
					return {
						'click @ui.tabsLabels': 'onTabClick'
					};
				},

				onRender: function () {
					if (this.model.get('slug') === WBcomEssentialelementorSectionsEditor.getTab()) {
						this.ui.tabsInput.attr('checked', 'checked');
					}
				},

				onTabClick: function (event) {

					var $clickedInput = jQuery(event.target);
					WBcomEssentialelementorSectionsEditor.setTab($clickedInput.val());
				}

			});

			self.FiltersCollectionView = Marionette.CompositeView.extend({

				id: 'wbcomessentialelementor-template-library-filters',

				template: '#tmpl-wbcomessentialelementor-template-modal-filters',

				childViewContainer: '#wbcomessentialelementor-modal-filters-container',

				getChildView: function (childModel) {
					return self.FiltersItemView;
				}

			});

			self.ModalTabsCollectionView = Marionette.CompositeView.extend({

				template: '#tmpl-wbcomessentialelementor-template-modal-tabs',

				childViewContainer: '#wbcomessentialelementor-modal-tabs-items',

				initialize: function () {
					this.listenTo(WBcomEssentialelementorSectionsEditor.channels.layout, 'tamplate:cloned', this._renderChildren);
				},

				getChildView: function (childModel) {
					return self.ModalTabsItemView;
				}

			});

			self.ModalTemplateItemView = Marionette.ItemView.extend({

				template: '#tmpl-wbcomessentialelementor-template-modal-item',

				className: function () {

					var urlClass = ' wbcomessentialelementor-template-has-url',
						sourceClass = ' elementor-template-library-template-';

					sourceClass += 'remote';

					return 'elementor-template-library-template' + sourceClass + urlClass;
				},

				ui: function () {
					return {
						previewButton: '.elementor-template-library-template-preview',
					};
				},

				behaviors: {
					insertTemplate: {
						behaviorClass: self.ModalInsertTemplateBehavior
					}
				}
			});

			self.ModalCollectionView = Marionette.CompositeView.extend({

				template: '#tmpl-wbcomessentialelementor-template-modal-templates',

				id: 'wbcomessentialelementor-template-library-templates',

				childViewContainer: '#wbcomessentialelementor-modal-templates-container',

				emptyView: function emptyView() {

					return new self.TemplatesEmptyView();
				},

				initialize: function () {

					this.listenTo(WBcomEssentialelementorSectionsEditor.channels.templates, 'filter:change', this._renderChildren);
				},

				filter: function (childModel) {

					var filter = WBcomEssentialelementorSectionsEditor.getFilter('category');
					var searchText = WBcomEssentialelementorSectionsEditor.getFilter('text');
					
					// Early return for no filters
					if (!filter && !searchText) {
						return true;
					}

					var matchesCategory = !filter || _.contains(childModel.get('categories'), filter);
					var matchesSearch = !searchText || childModel.get('title').toLowerCase().indexOf(searchText.toLowerCase()) >= 0;
					console.log(matchesSearch);
					return matchesCategory && matchesSearch;

				},

				getChildView: function (childModel) {
					return self.ModalTemplateItemView;
				},

				onRenderCollection: function () {

					var container = this.$childViewContainer,
						items = this.$childViewContainer.children(),
						tab = WBcomEssentialelementorSectionsEditor.getTab();

					if ('wbcom_essential_elementor_sections_page' === tab || 'local' === tab) {
						return;
					}

					// Wait for thumbnails to be loaded.
					container.imagesLoaded(function () { }).done(function () {
						self.masonry.init({
							container: container,
							items: items
						});
					});
				}

			});

			self.ModalLayoutView = Marionette.LayoutView.extend({

				el: '#wbcomessentialelementor-template-modal',

				regions: WBcomEssentialelementorSectionsData.modalRegions,

				initialize: function () {

					this.getRegion('modalHeader').show(new self.ModalHeaderView());
					this.listenTo(WBcomEssentialelementorSectionsEditor.channels.tabs, 'filter:change', this.switchTabs);

				},

				switchTabs: function () {
					this.showLoadingView();
					WBcomEssentialelementorSectionsEditor.requestTemplates(WBcomEssentialelementorSectionsEditor.getTab());
				},

				getHeaderView: function () {
					return this.getRegion('modalHeader').currentView;
				},

				getContentView: function () {
					return this.getRegion('modalContent').currentView;
				},

				showLoadingView: function () {
					this.modalContent.show(new self.ModalLoadingView());
				},

				showError: function () {
					this.modalContent.show(new self.ModalErrorView());
				},

				showTemplatesView: function (templatesCollection, categoriesCollection ) {

					if( 0 !== templatesCollection.length ) {
						this.getRegion('modalContent').show(new self.ModalBodyView());
						var contentView = this.getContentView(),
							header = this.getHeaderView();

						WBcomEssentialelementorSectionsEditor.collections.tabs = new self.ModalTabsCollection(WBcomEssentialelementorSectionsEditor.getTabs());

						header.headerTabs.show(new self.ModalTabsCollectionView({
							collection: WBcomEssentialelementorSectionsEditor.collections.tabs
						}));

						contentView.contentTemplates.show(new self.ModalCollectionView({
							collection: templatesCollection
						}));

						contentView.contentFilters.show(new self.FiltersCollectionView({
							collection: categoriesCollection
						}));

						contentView.contentSearch.show(new self.TemplateSearchCollectionView());

					} else {
						this.getRegion('modalContent').show(new self.TemplatesEmptyView());
					}

				}

			});

			self.ModalLoadingView = Marionette.ItemView.extend({
				id: 'wbcomessentialelementor-template-modal-loading',
				template: '#tmpl-wbcomessentialelementor-template-modal-loading'
			});

			self.ModalErrorView = Marionette.ItemView.extend({
				id: 'wbcomessentialelementor-template-modal-error',
				template: '#tmpl-wbcomessentialelementor-template-modal-error'
			});

		},

		masonry: {

			self: {},
			elements: {},

			init: function (settings) {

				var self = this;
				self.settings = $.extend(self.getDefaultSettings(), settings);
				self.elements = self.getDefaultElements();

				self.run();
			},

			getSettings: function (key) {
				if (key) {
					return this.settings[key];
				} else {
					return this.settings;
				}
			},

			getDefaultSettings: function () {
				return {
					container: null,
					items: null,
					columnsCount: 3,
					verticalSpaceBetween: 30
				};
			},

			getDefaultElements: function () {
				return {
					$container: jQuery(this.getSettings('container')),
					$items: jQuery(this.getSettings('items'))
				};
			},

			run: function () {
				var heights = [],
					distanceFromTop = this.elements.$container.position().top,
					settings = this.getSettings(),
					columnsCount = settings.columnsCount;

				distanceFromTop += parseInt(this.elements.$container.css('margin-top'), 10);

				this.elements.$container.height('');

				this.elements.$items.each(function (index) {
					var row = Math.floor(index / columnsCount),
						indexAtRow = index % columnsCount,
						$item = jQuery(this),
						itemPosition = $item.position(),
						itemHeight = $item[0].getBoundingClientRect().height + settings.verticalSpaceBetween;

					if (row) {
						var pullHeight = itemPosition.top - distanceFromTop - heights[indexAtRow];
						pullHeight -= parseInt($item.css('margin-top'), 10);
						pullHeight *= -1;
						$item.css('margin-top', pullHeight + 'px');
						heights[indexAtRow] += itemHeight;
					} else {
						heights.push(itemHeight);
					}
				});

				this.elements.$container.height(Math.max.apply(Math, heights));
			}
		}

	};

	WBcomEssentialelementorSectionsEditor = {
		modal: false,
		layout: false,
		collections: {},
		tabs: {},
		defaultTab: '',
		channels: {},
		atIndex: null,

		init: function () {

			window.elementor.on(
				'document:loaded',
				window._.bind(WBcomEssentialelementorSectionsEditor.onPreviewLoaded, WBcomEssentialelementorSectionsEditor)
			);

			WBcomEssentialelementorSectionsEditorViews.init();

		},

		onPreviewLoaded: function () {

			this.initWBcomEssentialelementorSectionsTempsButton();

			window.elementor.$previewContents.on(
				'click.addWBcomEssentialelementorSectionsTemplate',
				'.wbcom-essential-add-section-btn',
				_.bind(this.showTemplatesModal, this)
			);

			this.channels = {
				templates: Backbone.Radio.channel('WBcomEssentialelementor_EDITOR:templates'),
				tabs: Backbone.Radio.channel('WBcomEssentialelementor_EDITOR:tabs'),
				layout: Backbone.Radio.channel('WBcomEssentialelementor_EDITOR:layout'),
			};

			this.tabs = WBcomEssentialelementorSectionsData.tabs;
			this.defaultTab = WBcomEssentialelementorSectionsData.defaultTab;

		},

		initWBcomEssentialelementorSectionsTempsButton: function () {

			setTimeout(function () {
				var $addNewSection = window.elementor.$previewContents.find('.elementor-add-new-section'),
					addWBcomEssentialelementorSectionsTemplate = "<div class='elementor-add-section-area-button wbcom-essential-add-section-btn' title='Add Elementor Sections Template'><img src='"+WBcomEssentialelementorSectionsData.icon+"'></div>",
					$addWBcomEssentialelementorSectionsTemplate;

				if ($addNewSection.length) {
					$addWBcomEssentialelementorSectionsTemplate = $(addWBcomEssentialelementorSectionsTemplate).prependTo($addNewSection);
				}
			
				window.elementor.$previewContents.on(
					'click.addWBcomEssentialelementorSectionsTemplate',
					'.elementor-editor-section-settings .elementor-editor-element-add',
					function () {

						var $this = $(this),
							$section = $this.closest('.elementor-top-section'),
							modelID = $section.data('model-cid');



						if (-1 !== WBcomEssentialelementorSectionsData.Elementor_Version.indexOf('3.0.')) {
							if (window.elementor.previewView.collection.length) {
								$.each(window.elementor.previewView.collection.models, function (index, model) {
									if (modelID === model.cid) {
										WBcomEssentialelementorSectionsEditor.atIndex = index;
									}
								});
							}
						} else {
							if (window.elementor.sections.currentView.collection.length) {
								$.each(window.elementor.sections.currentView.collection.models, function (index, model) {
									if (modelID === model.cid) {
										WBcomEssentialelementorSectionsEditor.atIndex = index;
									}
								});
							}
						}


						setTimeout(function () {
							var $addNew = $section.prev('.elementor-add-section').find('.elementor-add-new-section');
							$addNew.prepend(addWBcomEssentialelementorSectionsTemplate);
						}, 100);

					}
				);
            }, 100);
		},

		getFilter: function (name) {

			return this.channels.templates.request('filter:' + name);
		},

		setFilter: function (name, value) {
			this.channels.templates.reply('filter:' + name, value);
			this.channels.templates.trigger('filter:change');
		},

		getTab: function () {
			return this.channels.tabs.request('filter:tabs');
		},

		setTab: function (value, silent) {

			this.channels.tabs.reply('filter:tabs', value);

			if (!silent) {
				this.channels.tabs.trigger('filter:change');
			}

		},

		getTabs: function () {

			var tabs = [];

			_.each(this.tabs, function (item, slug) {
				tabs.push({
					slug: slug,
					title: item.title
				});
			});

			return tabs;
		},

		showTemplatesModal: function () {

			this.getModal().show();

			if (!this.layout) {
				this.layout = new WBcomEssentialelementorSectionsEditorViews.ModalLayoutView();
				this.layout.showLoadingView();
			}

			this.setTab(this.defaultTab, true);
			this.requestTemplates(this.defaultTab);

		},

		requestTemplates: function (tabName) {
			
			if( '' === tabName ) {
				return;
			}

			var self = this,
				tab = self.tabs[tabName];

			self.setFilter('category', false);
			
			if (tab.data.templates && tab.data.categories) {
				self.layout.showTemplatesView(tab.data.templates, tab.data.categories);
			} else {
				$.ajax({
					url: ajaxurl,
					type: 'get',
					dataType: 'json',
					data: {
						action: 'wbcom_essential_elementor_sections_get_templates',
						tab: tabName,
						nonce: WBcomEssentialelementorSectionsData.nonce,
					},
					success: function (response) {
						console.log("%cTemplates Retrieved Successfully!!", "color: #7a7a7a; background-color: #eee;");

						var templates = new WBcomEssentialelementorSectionsEditorViews.LibraryCollection(response.data.templates),
							categories = new WBcomEssentialelementorSectionsEditorViews.CategoriesCollection(response.data.categories);

						self.tabs[tabName].data = {
							templates: templates,
							categories: categories,
						};

						self.layout.showTemplatesView(templates, categories );

					},
					error: function (err) {
						WBcomEssentialelementorSectionsEditor.closeModal();
					}
				});
			}

		},

		closeModal: function () {
			this.getModal().hide();
		},

		getModal: function () {

			if (!this.modal) {
				this.modal = elementor.dialogsManager.createWidget('lightbox', {
					id: 'wbcomessentialelementor-template-modal',
					className: 'elementor-templates-modal',
					closeButton: false
				});
			}

			return this.modal;

		}

	};

	$(window).on('elementor:init', WBcomEssentialelementorSectionsEditor.init);

})(jQuery);