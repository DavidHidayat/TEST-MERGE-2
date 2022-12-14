/**
 * An abstract base class which provides shared methods for Components across the Sencha product line.
 *
 * Please refer to sub class's documentation
 * @private
 */
Ext.define('Ext.AbstractComponent', {

    /* Begin Definitions */
    requires: [
        'Ext.ComponentQuery',
        'Ext.ComponentManager',
        'Ext.util.ProtoElement'
    ],

    mixins: {
        observable: 'Ext.util.Observable',
        animate: 'Ext.util.Animate',
        elementCt: 'Ext.util.ElementContainer',
        renderable: 'Ext.util.Renderable',
        state: 'Ext.state.Stateful'
    },

    // The "uses" property specifies class which are used in an instantiated AbstractComponent.
    // They do *not* have to be loaded before this class may be defined - that is what "requires" is for.
    uses: [
        'Ext.PluginManager',
        'Ext.Element',
        'Ext.DomHelper',
        'Ext.XTemplate',
        'Ext.ComponentQuery',
        'Ext.ComponentLoader',
        'Ext.EventManager',
        'Ext.layout.Context',
        'Ext.layout.Layout',
        'Ext.layout.component.Auto',
        'Ext.LoadMask',
        'Ext.ZIndexManager'
    ],

    statics: {
        AUTO_ID: 1000,

        pendingLayouts: null,

        layoutSuspendCount: 0,

        cancelLayout: function(comp) {
            var context = this.runningLayoutContext || this.pendingLayouts;

            if (context) {
                context.cancelComponent(comp);
            }
        },

        flushLayouts: function () {
            var me = this,
                context = me.pendingLayouts;

            if (context && context.invalidQueue.length) {
                me.pendingLayouts = null;
                me.runningLayoutContext = context;

                context.hookMethods({
                    runComplete: function () {
                        // we need to release the layout queue before running any of the
                        // finishedLayout calls because they call afterComponentLayout
                        // which can re-enter by calling doLayout/doComponentLayout.
                        me.runningLayoutContext = null;

                        return this.callParent(); // not "me" here!
                    }
                });

                context.run();
            }
        },

        resumeLayouts: function (flush) {
            if (this.layoutSuspendCount && ! --this.layoutSuspendCount) {
                if (flush) {
                    this.flushLayouts();
                }
            }
        },

        suspendLayouts: function () {
            ++this.layoutSuspendCount;
        },

        updateLayout: function (comp, defer) {
            var me = this,
                running = me.runningLayoutContext,
                pending;

            if (running) {
                running.queueInvalidate(comp);
            } else {
                pending = me.pendingLayouts || (me.pendingLayouts = new Ext.layout.Context());
                pending.queueInvalidate(comp);

                if (!defer && !me.layoutSuspendCount && !comp.isLayoutSuspended()) {
                    me.flushLayouts();
                }
            }
        }
    },

    /* End Definitions */

    /**
     * @property {Boolean} isComponent
     * `true` in this class to identify an object as an instantiated Component, or subclass thereof.
     */
    isComponent: true,

     /**
     * @private
     */
    getAutoId: function() {
        this.autoGenId = true;
        return ++Ext.AbstractComponent.AUTO_ID;
    },

    deferLayouts: false,

    /**
     * @cfg {String} id
     * The **unique id of this component instance.**
     *
     * It should not be necessary to use this configuration except for singleton objects in your application. Components
     * created with an id may be accessed globally using {@link Ext#getCmp Ext.getCmp}.
     *
     * Instead of using assigned ids, use the {@link #itemId} config, and {@link Ext.ComponentQuery ComponentQuery}
     * which provides selector-based searching for Sencha Components analogous to DOM querying. The {@link
     * Ext.container.Container Container} class contains {@link Ext.container.Container#down shortcut methods} to query
     * its descendant Components by selector.
     *
     * Note that this id will also be used as the element id for the containing HTML element that is rendered to the
     * page for this component. This allows you to write id-based CSS rules to style the specific instance of this
     * component uniquely, and also to select sub-elements using this component's id as the parent.
     *
     * **Note**: to avoid complications imposed by a unique id also see `{@link #itemId}`.
     *
     * **Note**: to access the container of a Component see `{@link #ownerCt}`.
     *
     * Defaults to an {@link #getId auto-assigned id}.
     */

     /**
     * @property {Boolean} autoGenId
     * `true` indicates an id was auto-generated rather than provided by configuration.
     * @private
     */
    autoGenId: false,

    /**
     * @cfg {String} itemId
     * An itemId can be used as an alternative way to get a reference to a component when no object reference is
     * available. Instead of using an `{@link #id}` with {@link Ext}.{@link Ext#getCmp getCmp}, use `itemId` with
     * {@link Ext.container.Container}.{@link Ext.container.Container#getComponent getComponent} which will retrieve
     * `itemId`'s or {@link #id}'s. Since `itemId`'s are an index to the container's internal MixedCollection, the
     * `itemId` is scoped locally to the container -- avoiding potential conflicts with {@link Ext.ComponentManager}
     * which requires a **unique** `{@link #id}`.
     *
     *     var c = new Ext.panel.Panel({ //
     *         {@link Ext.Component#height height}: 300,
     *         {@link #renderTo}: document.body,
     *         {@link Ext.container.Container#layout layout}: 'auto',
     *         {@link Ext.container.Container#cfg-items items}: [
     *             {
     *                 itemId: 'p1',
     *                 {@link Ext.panel.Panel#title title}: 'Panel 1',
     *                 {@link Ext.Component#height height}: 150
     *             },
     *             {
     *                 itemId: 'p2',
     *                 {@link Ext.panel.Panel#title title}: 'Panel 2',
     *                 {@link Ext.Component#height height}: 150
     *             }
     *         ]
     *     })
     *     p1 = c.{@link Ext.container.Container#getComponent getComponent}('p1'); // not the same as {@link Ext#getCmp Ext.getCmp()}
     *     p2 = p1.{@link #ownerCt}.{@link Ext.container.Container#getComponent getComponent}('p2'); // reference via a sibling
     *
     * Also see {@link #id}, `{@link Ext.container.Container#query}`, `{@link Ext.container.Container#down}` and
     * `{@link Ext.container.Container#child}`.
     *
     * **Note**: to access the container of an item see {@link #ownerCt}.
     */

    /**
     * @property {Ext.Container} ownerCt
     * This Component's owner {@link Ext.container.Container Container} (is set automatically
     * when this Component is added to a Container).
     *
     * **Note**: to access items within the Container see {@link #itemId}.
     * @readonly
     */

    /**
     * @cfg {String/Object} autoEl
     * A tag name or {@link Ext.DomHelper DomHelper} spec used to create the {@link #getEl Element} which will
     * encapsulate this Component.
     *
     * You do not normally need to specify this. For the base classes {@link Ext.Component} and
     * {@link Ext.container.Container}, this defaults to **'div'**. The more complex Sencha classes use a more
     * complex DOM structure specified by their own {@link #renderTpl}s.
     *
     * This is intended to allow the developer to create application-specific utility Components encapsulated by
     * different DOM elements. Example usage:
     *
     *     {
     *         xtype: 'component',
     *         autoEl: {
     *             tag: 'img',
     *             src: 'http://www.example.com/example.jpg'
     *         }
     *     }, {
     *         xtype: 'component',
     *         autoEl: {
     *             tag: 'blockquote',
     *             html: 'autoEl is cool!'
     *         }
     *     }, {
     *         xtype: 'container',
     *         autoEl: 'ul',
     *         cls: 'ux-unordered-list',
     *         items: {
     *             xtype: 'component',
     *             autoEl: 'li',
     *             html: 'First list item'
     *         }
     *     }
     */

    /**
     * @cfg {Ext.XTemplate/String/String[]} renderTpl
     * An {@link Ext.XTemplate XTemplate} used to create the internal structure inside this Component's encapsulating
     * {@link #getEl Element}.
     *
     * You do not normally need to specify this. For the base classes {@link Ext.Component} and
     * {@link Ext.container.Container}, this defaults to **`null`** which means that they will be initially rendered
     * with no internal structure; they render their {@link #getEl Element} empty. The more specialized ExtJS and Touch
     * classes which use a more complex DOM structure, provide their own template definitions.
     *
     * This is intended to allow the developer to create application-specific utility Components with customized
     * internal structure.
     *
     * Upon rendering, any created child elements may be automatically imported into object properties using the
     * {@link #renderSelectors} and {@link #childEls} options.
     * @protected
     */
    renderTpl: '{%this.renderContent(out,values)%}',

    /**
     * @cfg {Object} renderData
     *
     * The data used by {@link #renderTpl} in addition to the following property values of the component:
     *
     * - id
     * - ui
     * - uiCls
     * - baseCls
     * - componentCls
     * - frame
     *
     * See {@link #renderSelectors} and {@link #childEls} for usage examples.
     */

    /**
     * @cfg {Object} renderSelectors
     * An object containing properties specifying {@link Ext.DomQuery DomQuery} selectors which identify child elements
     * created by the render process.
     *
     * After the Component's internal structure is rendered according to the {@link #renderTpl}, this object is iterated through,
     * and the found Elements are added as properties to the Component using the `renderSelector` property name.
     *
     * For example, a Component which renderes a title and description into its element:
     *
     *     Ext.create('Ext.Component', {
     *         renderTo: Ext.getBody(),
     *         renderTpl: [
     *             '<h1 class="title">{title}</h1>',
     *             '<p>{desc}</p>'
     *         ],
     *         renderData: {
     *             title: "Error",
     *             desc: "Something went wrong"
     *         },
     *         renderSelectors: {
     *             titleEl: 'h1.title',
     *             descEl: 'p'
     *         },
     *         listeners: {
     *             afterrender: function(cmp){
     *                 // After rendering the component will have a titleEl and descEl properties
     *                 cmp.titleEl.setStyle({color: "red"});
     *             }
     *         }
     *     });
     *
     * For a faster, but less flexible, alternative that achieves the same end result (properties for child elements on the
     * Component after render), see {@link #childEls} and {@link #addChildEls}.
     */

    /**
     * @cfg {Object[]} childEls
     * An array describing the child elements of the Component. Each member of the array
     * is an object with these properties:
     *
     * - `name` - The property name on the Component for the child element.
     * - `itemId` - The id to combine with the Component's id that is the id of the child element.
     * - `id` - The id of the child element.
     *
     * If the array member is a string, it is equivalent to `{ name: m, itemId: m }`.
     *
     * For example, a Component which renders a title and body text:
     *
     *     Ext.create('Ext.Component', {
     *         renderTo: Ext.getBody(),
     *         renderTpl: [
     *             '<h1 id="{id}-title">{title}</h1>',
     *             '<p>{msg}</p>',
     *         ],
     *         renderData: {
     *             title: "Error",
     *             msg: "Something went wrong"
     *         },
     *         childEls: ["title"],
     *         listeners: {
     *             afterrender: function(cmp){
     *                 // After rendering the component will have a title property
     *                 cmp.title.setStyle({color: "red"});
     *             }
     *         }
     *     });
     *
     * A more flexible, but somewhat slower, approach is {@link #renderSelectors}.
     */

    /**
     * @cfg {String/HTMLElement/Ext.Element} renderTo
     * Specify the id of the element, a DOM element or an existing Element that this component will be rendered into.
     *
     * **Notes:**
     *
     * Do *not* use this option if the Component is to be a child item of a {@link Ext.container.Container Container}.
     * It is the responsibility of the {@link Ext.container.Container Container}'s
     * {@link Ext.container.Container#layout layout manager} to render and manage its child items.
     *
     * When using this config, a call to render() is not required.
     *
     * See `{@link #render}` also.
     */

    /**
     * @cfg {Boolean} frame
     * Specify as `true` to have the Component inject framing elements within the Component at render time to provide a
     * graphical rounded frame around the Component content.
     *
     * This is only necessary when running on outdated, or non standard-compliant browsers such as Microsoft's Internet
     * Explorer prior to version 9 which do not support rounded corners natively.
     *
     * The extra space taken up by this framing is available from the read only property {@link #frameSize}.
     */

    /**
     * @property {Object} frameSize
     * @readonly
     * Indicates the width of any framing elements which were added within the encapsulating element
     * to provide graphical, rounded borders. See the {@link #frame} config.
     *
     * This is an object containing the frame width in pixels for all four sides of the Component containing the
     * following properties:
     *
     * @property {Number} frameSize.top The width of the top framing element in pixels.
     * @property {Number} frameSize.right The width of the right framing element in pixels.
     * @property {Number} frameSize.bottom The width of the bottom framing element in pixels.
     * @property {Number} frameSize.left The width of the left framing element in pixels.
     */

    /**
     * @cfg {String/Object} componentLayout
     * The sizing and positioning of a Component's internal Elements is the responsibility of the Component's layout
     * manager which sizes a Component's internal structure in response to the Component being sized.
     *
     * Generally, developers will not use this configuration as all provided Components which need their internal
     * elements sizing (Such as {@link Ext.form.field.Base input fields}) come with their own componentLayout managers.
     *
     * The {@link Ext.layout.container.Auto default layout manager} will be used on instances of the base Ext.Component
     * class which simply sizes the Component's encapsulating element to the height and width specified in the
     * {@link #setSize} method.
     */

    /**
     * @cfg {Ext.XTemplate/Ext.Template/String/String[]} tpl
     * An {@link Ext.Template}, {@link Ext.XTemplate} or an array of strings to form an Ext.XTemplate. Used in
     * conjunction with the `{@link #data}` and `{@link #tplWriteMode}` configurations.
     */

    /**
     * @cfg {Object} data
     * The initial set of data to apply to the `{@link #tpl}` to update the content area of the Component.
     */

    /**
     * @cfg {String} xtype
     * This property provides a shorter alternative to creating objects than using a full
     * class name. Using `xtype` is the most common way to define component instances,
     * especially in a container. For example, the items in a form containing text fields
     * could be created explicitly like so:
     *
     *      items: [
     *          Ext.create('Ext.form.field.Text', {
     *              fieldLabel: 'Foo'
     *          }),
     *          Ext.create('Ext.form.field.Text', {
     *              fieldLabel: 'Bar'
     *          }),
     *          Ext.create('Ext.form.field.Number', {
     *              fieldLabel: 'Num'
     *          })
     *      ]
     *
     * But by using `xtype`, the above becomes:
     *
     *      items: [
     *          {
     *              xtype: 'textfield',
     *              fieldLabel: 'Foo'
     *          },
     *          {
     *              xtype: 'textfield',
     *              fieldLabel: 'Bar'
     *          },
     *          {
     *              xtype: 'numberfield',
     *              fieldLabel: 'Num'
     *          }
     *      ]
     *
     * When the `xtype` is common to many items, {@link Ext.container.AbstractContainer#defaultType}
     * is another way to specify the `xtype` for all items that don't have an explicit `xtype`:
     *
     *      defaultType: 'textfield',
     *      items: [
     *          { fieldLabel: 'Foo' },
     *          { fieldLabel: 'Bar' },
     *          { fieldLabel: 'Num', xtype: 'numberfield' }
     *      ]
     *
     * Each member of the `items` array is now just a "configuration object". These objects
     * are used to create and configure component instances. A configuration object can be
     * manually used to instantiate a component using {@link Ext#widget}:
     *
     *      var text1 = Ext.create('Ext.form.field.Text', {
     *          fieldLabel: 'Foo'
     *      });
     *
     *      // or alternatively:
     *
     *      var text1 = Ext.widget({
     *          xtype: 'textfield',
     *          fieldLabel: 'Foo'
     *      });
     *
     * This conversion of configuration objects into instantiated components is done when
     * a container is created as part of its {Ext.container.AbstractContainer#initComponent}
     * process. As part of the same process, the `items` array is converted from its raw
     * array form into a {@link Ext.util.MixedCollection} instance.
     *
     * You can define your own `xtype` on a custom {@link Ext.Component component} by specifying
     * the `xtype` property in {@link Ext#define}. For example:
     *
     *     Ext.define('MyApp.PressMeButton', {
     *         extend: 'Ext.button.Button',
     *         xtype: 'pressmebutton',
     *         text: 'Press Me'
     *     });
     *
     * Care should be taken when naming an `xtype` in a custom component because there is
     * a single, shared scope for all xtypes. Third part components should consider using
     * a prefix to avoid collisions.
     *
     *     Ext.define('Foo.form.CoolButton', {
     *         extend: 'Ext.button.Button',
     *         xtype: 'ux-coolbutton',
     *         text: 'Cool!'
     *     });
     */

    /**
     * @cfg {String} tplWriteMode
     * The Ext.(X)Template method to use when updating the content area of the Component.
     * See `{@link Ext.XTemplate#overwrite}` for information on default mode.
     */
    tplWriteMode: 'overwrite',

    /**
     * @cfg {String} [baseCls='x-component']
     * The base CSS class to apply to this components's element. This will also be prepended to elements within this
     * component like Panel's body will get a class x-panel-body. This means that if you create a subclass of Panel, and
     * you want it to get all the Panels styling for the element and the body, you leave the baseCls x-panel and use
     * componentCls to add specific styling for this component.
     */
    baseCls: Ext.baseCSSPrefix + 'component',

    /**
     * @cfg {String} componentCls
     * CSS Class to be added to a components root level element to give distinction to it via styling.
     */

    /**
     * @cfg {String} [cls='']
     * An optional extra CSS class that will be added to this component's Element. This can be useful
     * for adding customized styles to the component or any of its children using standard CSS rules.
     */

    /**
     * @cfg {String} [overCls='']
     * An optional extra CSS class that will be added to this component's Element when the mouse moves over the Element,
     * and removed when the mouse moves out. This can be useful for adding customized 'active' or 'hover' styles to the
     * component or any of its children using standard CSS rules.
     */

    /**
     * @cfg {String} [disabledCls='x-item-disabled']
     * CSS class to add when the Component is disabled. Defaults to 'x-item-disabled'.
     */
    disabledCls: Ext.baseCSSPrefix + 'item-disabled',

    /**
     * @cfg {String/String[]} ui
     * A set style for a component. Can be a string or an Array of multiple strings (UIs)
     */
    ui: 'default',

    /**
     * @cfg {String[]} uiCls
     * An array of of classNames which are currently applied to this component
     * @private
     */
    uiCls: [],

    /**
     * @cfg {String/Object} style
     * A custom style specification to be applied to this component's Element. Should be a valid argument to
     * {@link Ext.Element#applyStyles}.
     *
     *     new Ext.panel.Panel({
     *         title: 'Some Title',
     *         renderTo: Ext.getBody(),
     *         width: 400, height: 300,
     *         layout: 'form',
     *         items: [{
     *             xtype: 'textarea',
     *             style: {
     *                 width: '95%',
     *                 marginBottom: '10px'
     *             }
     *         },
     *         new Ext.button.Button({
     *             text: 'Send',
     *             minWidth: '100',
     *             style: {
     *                 marginBottom: '10px'
     *             }
     *         })
     *         ]
     *     });
     */

    /**
     * @cfg {Number} width
     * The width of this component in pixels.
     */

    /**
     * @cfg {Number} height
     * The height of this component in pixels.
     */

    /**
     * @cfg {Number/String} border
     * Specifies the border for this component. The border can be a single numeric value to apply to all sides or it can
     * be a CSS style specification for each style, for example: '10 5 3 10'.
     */

    /**
     * @cfg {Number/String} padding
     * Specifies the padding for this component. The padding can be a single numeric value to apply to all sides or it
     * can be a CSS style specification for each style, for example: '10 5 3 10'.
     */

    /**
     * @cfg {Number/String} margin
     * Specifies the margin for this component. The margin can be a single numeric value to apply to all sides or it can
     * be a CSS style specification for each style, for example: '10 5 3 10'.
     */

    /**
     * @cfg {Boolean} hidden
     * True to hide the component.
     */
    hidden: false,

    /**
     * @cfg {Boolean} disabled
     * True to disable the component.
     */
    disabled: false,

    /**
     * @cfg {Boolean} [draggable=false]
     * Allows the component to be dragged.
     */

    /**
     * @property {Boolean} draggable
     * Indicates whether or not the component can be dragged.
     * @readonly
     */
    draggable: false,

    /**
     * @cfg {Boolean} floating
     * Create the Component as a floating and use absolute positioning.
     *
     * The z-index of floating Components is handled by a ZIndexManager. If you simply render a floating Component into the DOM, it will be managed
     * by the global {@link Ext.WindowManager WindowManager}.
     *
     * If you include a floating Component as a child item of a Container, then upon render, ExtJS will seek an ancestor floating Component to house a new
     * ZIndexManager instance to manage its descendant floaters. If no floating ancestor can be found, the global WindowManager will be used.
     *
     * When a floating Component which has a ZindexManager managing descendant floaters is destroyed, those descendant floaters will also be destroyed.
     */
    floating: false,

    /**
     * @cfg {String} hideMode
     * A String which specifies how this Component's encapsulating DOM element will be hidden. Values may be:
     *
     *   - `'display'` : The Component will be hidden using the `display: none` style.
     *   - `'visibility'` : The Component will be hidden using the `visibility: hidden` style.
     *   - `'offsets'` : The Component will be hidden by absolutely positioning it out of the visible area of the document.
     *     This is useful when a hidden Component must maintain measurable dimensions. Hiding using `display` results in a
     *     Component having zero dimensions.
     */
    hideMode: 'display',

    /**
     * @cfg {String} contentEl
     * Specify an existing HTML element, or the `id` of an existing HTML element to use as the content for this component.
     *
     * This config option is used to take an existing HTML element and place it in the layout element of a new component
     * (it simply moves the specified DOM element _after the Component is rendered_ to use as the content.
     *
     * **Notes:**
     *
     * The specified HTML element is appended to the layout element of the component _after any configured
     * {@link #html HTML} has been inserted_, and so the document will not contain this element at the time
     * the {@link #render} event is fired.
     *
     * The specified HTML element used will not participate in any **`{@link Ext.container.Container#layout layout}`**
     * scheme that the Component may use. It is just HTML. Layouts operate on child
     * **`{@link Ext.container.Container#cfg-items items}`**.
     *
     * Add either the `x-hidden` or the `x-hide-display` CSS class to prevent a brief flicker of the content before it
     * is rendered to the panel.
     */

    /**
     * @cfg {String/Object} [html='']
     * An HTML fragment, or a {@link Ext.DomHelper DomHelper} specification to use as the layout element content.
     * The HTML content is added after the component is rendered, so the document will not contain this HTML at the time
     * the {@link #render} event is fired. This content is inserted into the body _before_ any configured {@link #contentEl}
     * is appended.
     */

    /**
     * @cfg {Boolean} styleHtmlContent
     * True to automatically style the html inside the content target of this component (body for panels).
     */
    styleHtmlContent: false,

    /**
     * @cfg {String} [styleHtmlCls='x-html']
     * The class that is added to the content target when you set styleHtmlContent to true.
     */
    styleHtmlCls: Ext.baseCSSPrefix + 'html',

    /**
     * @cfg {Number} minHeight
     * The minimum value in pixels which this Component will set its height to.
     *
     * **Warning:** This will override any size management applied by layout managers.
     */
    /**
     * @cfg {Number} minWidth
     * The minimum value in pixels which this Component will set its width to.
     *
     * **Warning:** This will override any size management applied by layout managers.
     */
    /**
     * @cfg {Number} maxHeight
     * The maximum value in pixels which this Component will set its height to.
     *
     * **Warning:** This will override any size management applied by layout managers.
     */
    /**
     * @cfg {Number} maxWidth
     * The maximum value in pixels which this Component will set its width to.
     *
     * **Warning:** This will override any size management applied by layout managers.
     */

    /**
     * @cfg {Ext.ComponentLoader/Object} loader
     * A configuration object or an instance of a {@link Ext.ComponentLoader} to load remote content for this Component.
     */

    /**
     * @cfg {Boolean} autoShow
     * True to automatically show the component upon creation. This config option may only be used for
     * {@link #floating} components or components that use {@link #autoRender}. Defaults to false.
     */
    autoShow: false,

    /**
     * @cfg {Boolean/String/HTMLElement/Ext.Element} autoRender
     * This config is intended mainly for non-{@link #floating} Components which may or may not be shown. Instead of using
     * {@link #renderTo} in the configuration, and rendering upon construction, this allows a Component to render itself
     * upon first _{@link #method-show}_. If {@link #floating} is true, the value of this config is omited as if it is `true`.
     *
     * Specify as `true` to have this Component render to the document body upon first show.
     *
     * Specify as an element, or the ID of an element to have this Component render to a specific element upon first
     * show.
     */
    autoRender: false,

    // @private
    allowDomMove: true,

    /**
     * @cfg {Object/Object[]} plugins
     * An object or array of objects that will provide custom functionality for this component. The only requirement for
     * a valid plugin is that it contain an init method that accepts a reference of type Ext.Component. When a component
     * is created, if any plugins are available, the component will call the init method on each plugin, passing a
     * reference to itself. Each plugin can then call methods or respond to events on the component as needed to provide
     * its functionality.
     */

    /**
     * @property {Boolean} rendered
     * Indicates whether or not the component has been rendered.
     * @readonly
     */
    rendered: false,

    /**
     * @property {Number} componentLayoutCounter
     * @private
     * The number of component layout calls made on this object.
     */
    componentLayoutCounter: 0,

    /**
     * @cfg {Boolean/Number} [shrinkWrap=2]
     *
     * If this property is a number, it is interpreted as follows:
     *
     *   - 0: Neither width nor height depend on content. This is equivalent to `false`.
     *   - 1: Width depends on content (shrink wraps), but height does not.
     *   - 2: Height depends on content (shrink wraps), but width does not. The default.
     *   - 3: Both width and height depend on content (shrink wrap). This is equivalent to `true`.
     *
     * In CSS terms, shrink-wrap width is analogous to an inline-block element as opposed
     * to a block-level element. Some container layouts always shrink-wrap their children,
     * effectively ignoring this property (e.g., {@link Ext.layout.container.HBox},
     * {@link Ext.layout.container.VBox}, {@link Ext.layout.component.Dock}).
     */
    shrinkWrap: 2,

    weight: 0,

    /**
     * @property {Boolean} maskOnDisable
     * This is an internal flag that you use when creating custom components. By default this is set to true which means
     * that every component gets a mask when its disabled. Components like FieldContainer, FieldSet, Field, Button, Tab
     * override this property to false since they want to implement custom disable logic.
     */
    maskOnDisable: true,

    /**
     * @property {Boolean} [_isLayoutRoot=false]
     * Setting this property to `true` causes the {@link #isLayoutRoot} method to return
     * `true` and stop the search for the top-most component for a layout.
     * @protected
     */
    _isLayoutRoot: false,

    /**
     * Creates new Component.
     * @param {Object} config  (optional) Config object.
     */
    constructor : function(config) {
        var me = this,
            i, len, xhooks;

        if (config) {
            Ext.apply(me, config);

            xhooks = me.xhooks;
            if (xhooks) {
                me.hookMethods(xhooks);
                delete me.xhooks;
            }
        } else {
            config = {};
        }

        me.initialConfig = config;

        me.mixins.elementCt.constructor.call(me);

        me.addEvents(
            /**
             * @event beforeactivate
             * Fires before a Component has been visually activated. Returning false from an event listener can prevent
             * the activate from occurring.
             * @param {Ext.Component} this
             */
            'beforeactivate',
            /**
             * @event activate
             * Fires after a Component has been visually activated.
             * @param {Ext.Component} this
             */
            'activate',
            /**
             * @event beforedeactivate
             * Fires before a Component has been visually deactivated. Returning false from an event listener can
             * prevent the deactivate from occurring.
             * @param {Ext.Component} this
             */
            'beforedeactivate',
            /**
             * @event deactivate
             * Fires after a Component has been visually deactivated.
             * @param {Ext.Component} this
             */
            'deactivate',
            /**
             * @event added
             * Fires after a Component had been added to a Container.
             * @param {Ext.Component} this
             * @param {Ext.container.Container} container Parent Container
             * @param {Number} pos position of Component
             */
            'added',
            /**
             * @event disable
             * Fires after the component is disabled.
             * @param {Ext.Component} this
             */
            'disable',
            /**
             * @event enable
             * Fires after the component is enabled.
             * @param {Ext.Component} this
             */
            'enable',
            /**
             * @event beforeshow
             * Fires before the component is shown when calling the {@link #show} method. Return false from an event
             * handler to stop the show.
             * @param {Ext.Component} this
             */
            'beforeshow',
            /**
             * @event show
             * Fires after the component is shown when calling the {@link #show} method.
             * @param {Ext.Component} this
             */
            'show',
            /**
             * @event beforehide
             * Fires before the component is hidden when calling the {@link #hide} method. Return false from an event
             * handler to stop the hide.
             * @param {Ext.Component} this
             */
            'beforehide',
            /**
             * @event hide
             * Fires after the component is hidden. Fires after the component is hidden when calling the {@link #hide}
             * method.
             * @param {Ext.Component} this
             */
            'hide',
            /**
             * @event removed
             * Fires when a component is removed from an Ext.container.Container
             * @param {Ext.Component} this
             * @param {Ext.container.Container} ownerCt Container which holds the component
             */
            'removed',
            /**
             * @event beforerender
             * Fires before the component is {@link #rendered}. Return false from an event handler to stop the
             * {@link #render}.
             * @param {Ext.Component} this
             */
            'beforerender',
            /**
             * @event render
             * Fires after the component markup is {@link #rendered}.
             * @param {Ext.Component} this
             */
            'render',
            /**
             * @event afterrender
             * Fires after the component rendering is finished.
             *
             * The afterrender event is fired after this Component has been {@link #rendered}, been postprocesed by any
             * afterRender method defined for the Component.
             * @param {Ext.Component} this
             */
            'afterrender',
            /**
             * @event boxready
             * Fires *one time* - after the component has been layed out for the first time at its initial size.
             * @param {Ext.Component} this
             * @param {Number} width The initial width
             * @param {Number} height The initial height
             */
            'boxready',
            /**
             * @event beforedestroy
             * Fires before the component is {@link #method-destroy}ed. Return false from an event handler to stop the
             * {@link #method-destroy}.
             * @param {Ext.Component} this
             */
            'beforedestroy',
            /**
             * @event destroy
             * Fires after the component is {@link #method-destroy}ed.
             * @param {Ext.Component} this
             */
            'destroy',
            /**
             * @event resize
             * Fires after the component is resized. Note that this does *not* fire when the component is first layed out at its initial
             * size. To hook that point in the lifecycle, use the {@link #boxready} event.
             * @param {Ext.Component} this
             * @param {Number} width The new width that was set
             * @param {Number} height The new height that was set
             * @param {Number} oldWidth The previous width
             * @param {Number} oldHeight The previous height
             */
            'resize',
            /**
             * @event move
             * Fires after the component is moved.
             * @param {Ext.Component} this
             * @param {Number} x The new x position
             * @param {Number} y The new y position
             */
             'move',
            /**
             * @event focus
             * Fires when this Component receives focus.
             * @param {Ext.Component} this
             * @param {Ext.EventObject} The focus event.
             */
            'focus',
            /**
             * @event blur
             * Fires when this Component loses focus.
             * @param {Ext.Component} this
             * @param {Ext.EventObject} The blur event.
             */
            'blur'
        );

        me.getId();

        me.setupProtoEl();

        // initComponent, beforeRender, or event handlers may have set the style or cls property since the protoEl was set up
        // so we must apply styles and classes here too.
        if (me.cls) {
            me.initialCls = me.cls;
            me.protoEl.addCls(me.cls);
        }
        if (me.style) {
            me.initialStyle = me.style;
            me.protoEl.setStyle(me.style);
        }

        me.mons = [];
        me.renderData = me.renderData || {};
        me.renderSelectors = me.renderSelectors || {};

        if (me.plugins) {
            me.plugins = [].concat(me.plugins);
            me.constructPlugins();
        }

        // Hash of event "hasListeners" flags.
        // For repeated events in time-critical code, the firing code should use
        // if (!me.hasListeners.beforerender || me.fireEvent('beforerender', me) !== false) { //code... }
        // Bubbling the events counts as one listener. initComponent may add listeners, so needs setting up now.
        me.hasListeners = me.hasListeners || new me.HasListeners();

        me.initComponent();

        // ititComponent gets a chance to change the id property before registering
        Ext.ComponentManager.register(me);

        // Dont pass the config so that it is not applied to 'this' again
        me.mixins.observable.constructor.call(me);
        me.mixins.state.constructor.call(me, config);

        // Save state on resize.
        this.addStateEvents('resize');

        // Move this into Observable?
        if (me.plugins) {
            me.plugins = [].concat(me.plugins);
            for (i = 0, len = me.plugins.length; i < len; i++) {
                me.plugins[i] = me.initPlugin(me.plugins[i]);
            }
        }

        me.loader = me.getLoader();

        if (me.renderTo) {
            me.render(me.renderTo);
            // EXTJSIV-1935 - should be a way to do afterShow or something, but that
            // won't work. Likewise, rendering hidden and then showing (w/autoShow) has
            // implications to afterRender so we cannot do that.
        }

        if (me.autoShow) {
            me.show();
        }

        //<debug>
        if (Ext.isDefined(me.disabledClass)) {
            if (Ext.isDefined(Ext.global.console)) {
                Ext.global.console.warn('Ext.Component: disabledClass has been deprecated. Please use disabledCls.');
            }
            me.disabledCls = me.disabledClass;
            delete me.disabledClass;
        }
        //</debug>
    },

    initComponent: function () {
        // This is called again here to allow derived classes to add plugin configs to the
        // plugins array before calling down to this, the base initComponent.
        this.constructPlugins();

        // this will properly (ignore or) constrain the configured width/height to their
        // min/max values for consistency.
        this.setSize(this.width, this.height);
    },

    /**
     * The supplied default state gathering method for the AbstractComponent class.
     *
     * This method returns dimension settings such as `flex`, `anchor`, `width` and `height` along with `collapsed`
     * state.
     *
     * Subclasses which implement more complex state should call the superclass's implementation, and apply their state
     * to the result if this basic state is to be saved.
     *
     * Note that Component state will only be saved if the Component has a {@link #stateId} and there as a StateProvider
     * configured for the document.
     *
     * @return {Object}
     */
    getState: function() {
        var me = this,
            state = null,
            sizeModel = me.getSizeModel();

        if (sizeModel.width.configured) {
            state = me.addPropertyToState(state, 'width');
        }
        if (sizeModel.height.configured) {
            state = me.addPropertyToState(state, 'height');
        }

        return state;
    },

    /**
     * Save a property to the given state object if it is not its default or configured
     * value.
     *
     * @param {Object} state The state object
     * @param {String} propName The name of the property on this object to save.
     * @param {String} [value] The value of the state property (defaults to `this[propName]`).
     * @return {Boolean} The state object or a new object if state was null and the property
     * was saved.
     * @protected
     */
    addPropertyToState: function (state, propName, value) {
        var me = this,
            len = arguments.length;

        // If the property is inherited, it is a default and we don't want to save it to
        // the state, however if we explicitly specify a value, always save it
        if (len == 3 || me.hasOwnProperty(propName)) {
            if (len < 3) {
                value = me[propName];
            }

            // If the property has the same value as was initially configured, again, we
            // don't want to save it.
            if (value !== me.initialConfig[propName]) {
                (state || (state = {}))[propName] = value;
            }
        }

        return state;
    },

    show: Ext.emptyFn,

    animate: function(animObj) {
        var me = this,
            hasToWidth,
            hasToHeight,
            toHeight,
            toWidth,
            to,
            clearWidth,
            clearHeight,
            curWidth, w, curHeight, h, needsResize;

        animObj = animObj || {};
        to = animObj.to || {};

        if (Ext.fx.Manager.hasFxBlock(me.id)) {
            return me;
        }

        hasToWidth = Ext.isDefined(to.width);
        if (hasToWidth) {
            toWidth = Ext.Number.constrain(to.width, me.minWidth, me.maxWidth);
        }

        hasToHeight = Ext.isDefined(to.height);
        if (hasToHeight) {
            toHeight = Ext.Number.constrain(to.height, me.minHeight, me.maxHeight);
        }

        // Special processing for animating Component dimensions.
        if (!animObj.dynamic && (hasToWidth || hasToHeight)) {
            curWidth = (animObj.from ? animObj.from.width : undefined) || me.getWidth();
            w = curWidth;
            curHeight = (animObj.from ? animObj.from.height : undefined) || me.getHeight();
            h = curHeight;
            needsResize = false;

            if (hasToHeight && toHeight > curHeight) {
                h = toHeight;
                needsResize = true;
            }
            if (hasToWidth && toWidth > curWidth) {
                w = toWidth;
                needsResize = true;
            }

            // If any dimensions are being increased, we must resize the internal structure
            // of the Component, but then clip it by sizing its encapsulating element back to original dimensions.
            // The animation will then progressively reveal the larger content.
            if (needsResize) {
                clearWidth = !Ext.isNumber(me.width);
                clearHeight = !Ext.isNumber(me.height);

                me.setSize(w, h);
                me.el.setSize(curWidth, curHeight);
                if (clearWidth) {
                    delete me.width;
                }
                if (clearHeight) {
                    delete me.height;
                }
            }
            if (hasToWidth) {
                to.width = toWidth;
            }

            if (hasToHeight) {
                to.height = toHeight;
            }
        }
        return me.mixins.animate.animate.apply(me, arguments);
    },

    onHide: function() {
        this.updateLayout({ isRoot: false });
    },

    onShow : function() {
        this.updateLayout({ isRoot: false });
    },

    constructPlugin: function(plugin) {
        if (plugin.ptype && typeof plugin.init != 'function') {
            plugin.cmp = this;
            plugin = Ext.PluginManager.create(plugin);
        }
        else if (typeof plugin == 'string') {
            plugin = Ext.PluginManager.create({
                ptype: plugin,
                cmp: this
            });
        }
        return plugin;
    },

    /**
     * Ensures that the plugins array contains fully constructed plugin instances. This converts any configs into their
     * appropriate instances.
     */
    constructPlugins: function() {
        var me = this,
            plugins = me.plugins,
            i, len;

        if (plugins) {
            for (i = 0, len = plugins.length; i < len; i++) {
                // this just returns already-constructed plugin instances...
                plugins[i] = me.constructPlugin(plugins[i]);
            }
        }
    },

    // @private
    initPlugin : function(plugin) {
        plugin.init(this);

        return plugin;
    },

    /**
     * @private
     * Injected as an override by Ext.Aria.initialize
     */
    updateAria: Ext.emptyFn,

    /**
     * Called by Component#doAutoRender
     *
     * Register a Container configured `floating: true` with this Component's {@link Ext.ZIndexManager ZIndexManager}.
     *
     * Components added in ths way will not participate in any layout, but will be rendered
     * upon first show in the way that {@link Ext.window.Window Window}s are.
     */
    registerFloatingItem: function(cmp) {
        var me = this;
        if (!me.floatingItems) {
            me.floatingItems = new Ext.ZIndexManager(me);
        }
        me.floatingItems.register(cmp);
    },

    unregisterFloatingItem: function(cmp) {
        var me = this;
        if (me.floatingItems) {
            me.floatingItems.unregister(cmp);
        }
    },

    layoutSuspendCount: 0,

    suspendLayouts: function () {
        var me = this;
        if (!me.rendered) {
            return;
        }
        if (++me.layoutSuspendCount == 1) {
            me.suspendLayout = true;
        }
    },

    resumeLayouts: function (flushOptions) {
        var me = this;
        if (!me.rendered) {
            return;
        }
        if (! --me.layoutSuspendCount) {
            me.suspendLayout = false;
            if (flushOptions && !me.isLayoutSuspended()) {
                me.updateLayout(flushOptions);
            }
        }
    },

    setupProtoEl: function() {
        var me = this,
            cls = [ me.baseCls, me.getComponentLayout().targetCls ];

        //<deprecated since=0.99>
        if (Ext.isDefined(me.cmpCls)) {
            if (Ext.isDefined(Ext.global.console)) {
                Ext.global.console.warn('Ext.Component: cmpCls has been deprecated. Please use componentCls.');
            }
            me.componentCls = me.cmpCls;
            delete me.cmpCls;
        }
        //</deprecated>

        if (me.componentCls) {
            cls.push(me.componentCls);
        } else {
            me.componentCls = me.baseCls;
        }

        me.protoEl = new Ext.util.ProtoElement({
            cls: cls.join(' ') // in case any of the parts have multiple classes
        });
    },

    /**
     * Sets the UI for the component. This will remove any existing UIs on the component. It will also loop through any
     * uiCls set on the component and rename them so they include the new UI
     * @param {String} ui The new UI for the component
     */
    setUI: function(ui) {
        var me = this,
            oldUICls = Ext.Array.clone(me.uiCls),
            newUICls = [],
            classes = [],
            cls,
            i;

        //loop through all exisiting uiCls and update the ui in them
        for (i = 0; i < oldUICls.length; i++) {
            cls = oldUICls[i];

            classes = classes.concat(me.removeClsWithUI(cls, true));
            newUICls.push(cls);
        }

        if (classes.length) {
            me.removeCls(classes);
        }

        //remove the UI from the element
        me.removeUIFromElement();

        //set the UI
        me.ui = ui;

        //add the new UI to the elemend
        me.addUIToElement();

        //loop through all exisiting uiCls and update the ui in them
        classes = [];
        for (i = 0; i < newUICls.length; i++) {
            cls = newUICls[i];
            classes = classes.concat(me.addClsWithUI(cls, true));
        }

        if (classes.length) {
            me.addCls(classes);
        }
    },

    /**
     * Adds a cls to the uiCls array, which will also call {@link #addUIClsToElement} and adds to all elements of this
     * component.
     * @param {String/String[]} classes A string or an array of strings to add to the uiCls
     * @param {Object} skip (Boolean) skip True to skip adding it to the class and do it later (via the return)
     */
    addClsWithUI: function(classes, skip) {
        var me = this,
            clsArray = [],
            length,
            i = 0,
            cls;

        if (typeof classes === "string") {
            classes = (classes.indexOf(' ') < 0) ? [classes] : Ext.String.splitWords(classes);
        }

        length = classes.length;

        me.uiCls = Ext.Array.clone(me.uiCls);

        for (; i < length; i++) {
            cls = classes[i];
            if (cls && !me.hasUICls(cls)) {
                me.uiCls.push(cls);
                clsArray = clsArray.concat(me.addUIClsToElement(cls));
            }
        }

        if (skip !== true) {
            me.addCls(clsArray);
        }

        return clsArray;
    },

    /**
     * Removes a cls to the uiCls array, which will also call {@link #removeUIClsFromElement} and removes it from all
     * elements of this component.
     * @param {String/String[]} cls A string or an array of strings to remove to the uiCls
     */
    removeClsWithUI: function(classes, skip) {
        var me = this,
            clsArray = [],
            i = 0,
            length, cls;

        if (typeof classes === "string") {
            classes = (classes.indexOf(' ') < 0) ? [classes] : Ext.String.splitWords(classes);
        }

        length = classes.length;

        for (i = 0; i < length; i++) {
            cls = classes[i];
            if (cls && me.hasUICls(cls)) {
                me.uiCls = Ext.Array.remove(me.uiCls, cls);
                clsArray = clsArray.concat(me.removeUIClsFromElement(cls));
            }
        }

        if (skip !== true) {
            me.removeCls(clsArray);
        }

        return clsArray;
    },

    /**
     * Checks if there is currently a specified uiCls
     * @param {String} cls The cls to check
     */
    hasUICls: function(cls) {
        var me = this,
            uiCls = me.uiCls || [];

        return Ext.Array.contains(uiCls, cls);
    },

    frameElementsArray: ['tl', 'tc', 'tr', 'ml', 'mc', 'mr', 'bl', 'bc', 'br'],

    /**
     * Method which adds a specified UI + uiCls to the components element. Can be overridden to remove the UI from more
     * than just the components element.
     * @param {String} ui The UI to remove from the element
     */
    addUIClsToElement: function(cls) {
        var me = this,
            baseClsUi = me.baseCls + '-' + me.ui + '-' + cls,
            result = [Ext.baseCSSPrefix + cls, me.baseCls + '-' + cls, baseClsUi],
            frameElementCls = me.frameElementCls,
            frameElementsArray, frameElementsLength, i, el, frameElement, c;

        if (me.frame && !Ext.supports.CSS3BorderRadius) {
            // define each element of the frame
            frameElementsArray = me.frameElementsArray;
            frameElementsLength = frameElementsArray.length;
            i = 0;

            // loop through each of them, and if they are defined add the ui
            for (; i < frameElementsLength; i++) {
                frameElement = frameElementsArray[i];
                el = me['frame' + frameElement.toUpperCase()];
                c = baseClsUi + '-' + frameElement;
                if (el && el.dom) {
                    el.addCls(c);
                } else if (Ext.Array.indexOf(frameElementCls[frameElement], c) == -1) {
                    frameElementCls[frameElement].push(c);
                }
            }
        }

        me.frameElementCls = frameElementCls;

        return result;
    },

    /**
     * Method which removes a specified UI + uiCls from the components element. The cls which is added to the element
     * will be: `this.baseCls + '-' + ui`
     * @param {String} ui The UI to add to the element
     */
    removeUIClsFromElement: function(cls) {
        var me = this,
            baseClsUi = me.baseCls + '-' + me.ui + '-' + cls,
            result = [Ext.baseCSSPrefix + cls, me.baseCls + '-' + cls, baseClsUi],
            frameElementCls = me.frameElementCls,
            frameElementsArray, frameElementsLength, i, el, frameElement, c;

        if (me.frame && !Ext.supports.CSS3BorderRadius) {
            // define each element of the frame
            frameElementsArray = me.frameElementsArray;
            frameElementsLength = frameElementsArray.length;
            i = 0;

            // loop through each of them, and if they are defined add the ui
            for (; i < frameElementsLength; i++) {
                frameElement = frameElementsArray[i];
                el = me['frame' + frameElement.toUpperCase()];
                c = baseClsUi + '-' + frameElement;
                if (el && el.dom) {
                    el.addCls(c);
                } else {
                    Ext.Array.remove(frameElementCls[frameElement], c);
                }
            }
        }

        me.frameElementCls = frameElementCls;

        return result;
    },

    /**
     * Method which adds a specified UI to the components element.
     * @private
     */
    addUIToElement: function() {
        var me = this,
            baseClsUI = me.baseCls + '-' + me.ui,
            frameElementCls = me.frameElementCls,
            frameElementsArray, frameElementsLength, i, el, frameElement, c;

        me.addCls(baseClsUI);

        if (me.frame && !Ext.supports.CSS3BorderRadius) {
            // define each element of the frame
            frameElementsArray = me.frameElementsArray;
            frameElementsLength = frameElementsArray.length;
            i = 0;

            // loop through each of them, and if they are defined add the ui
            for (; i < frameElementsLength; i++) {
                frameElement = frameElementsArray[i];
                el = me['frame' + frameElement.toUpperCase()];
                c = baseClsUI + '-' + frameElement;
                if (el) {
                    el.addCls(c);
                } else {
                    if (!Ext.Array.contains(frameElementCls[frameElement], c)) {
                        frameElementCls[frameElement].push(c);
                    }
                }
            }
        }
    },

    /**
     * Method which removes a specified UI from the components element.
     * @private
     */
    removeUIFromElement: function() {
        var me = this,
            baseClsUI = me.baseCls + '-' + me.ui,
            frameElementCls = me.frameElementCls,
            frameElementsArray, frameElementsLength, i, el, frameElement, c;

        me.removeCls(baseClsUI);

        if (me.frame && !Ext.supports.CSS3BorderRadius) {
            // define each element of the frame
            frameElementsArray = me.frameElementsArray;
            frameElementsLength = frameElementsArray.length;
            i = 0;

            for (; i < frameElementsLength; i++) {
                frameElement = frameElementsArray[i];
                el = me['frame' + frameElement.toUpperCase()];
                c = baseClsUI + '-' + frameElement;
                if (el) {
                    el.removeCls(c);
                } else {
                    Ext.Array.remove(frameElementCls[frameElement], c);
                }
            }
        }
    },

    /**
     * @private
     */
    getTpl: function(name) {
        return Ext.XTemplate.getTpl(this, name);
    },

    /**
     * Converts style definitions to String.
     * @return {String} A CSS style string with style, padding, margin and border.
     * @private
     */
    initStyles: function(targetEl) {
        var me = this,
            Element = Ext.Element,
            padding = me.padding,
            margin = me.margin,
            x = me.x,
            y = me.y,
            width, height;

        // Convert the padding, margin and border properties from a space seperated string
        // into a proper style string
        if (padding !== undefined) {
            targetEl.setStyle('padding', Element.unitizeBox((padding === true) ? 5 : padding));
        }

        if (margin !== undefined) {
            targetEl.setStyle('margin', Element.unitizeBox((margin === true) ? 5 : margin));
        }

        if (me.border !== undefined) {
            me.setBorder(me.border, targetEl);
        }

        // initComponent, beforeRender, or event handlers may have set the style or cls property since the protoEl was set up
        // so we must apply styles and classes here too.
        if (me.cls && me.cls != me.initialCls) {
            targetEl.addCls(me.cls);
            delete me.cls;
            delete me.initialCls;
        }
        if (me.style && me.style != me.initialStyle) {
            targetEl.setStyle(me.style);
            delete me.style;
            delete me.initialStyle;
        }

        if (x !== undefined) {
            targetEl.setStyle('left', x + 'px');
        }
        if (y !== undefined) {
            targetEl.setStyle('top', y + 'px');
        }

        // Framed components need their width/height to apply to the frame, which is
        // best handled in layout at present.
        // If we're using the content box model, we also cannot assign initial sizes since we do not know the border widths to subtract
        if (!me.getFrameInfo() && Ext.isBorderBox) {
            width = me.width;
            height = me.height;

            // framed components need their width/height to apply to the frame, which is
            // best handled in layout at present
            if (typeof width == 'number') {
                targetEl.setStyle('width', width + 'px');
            }
            if (typeof height == 'number') {
                targetEl.setStyle('height', height + 'px');
            }
        }
    },

    // @private
    initEvents : function() {
        var me = this,
            afterRenderEvents = me.afterRenderEvents,
            el,
            property,
            fn = function(listeners){
                me.mon(el, listeners);
            };

        if (afterRenderEvents) {
            for (property in afterRenderEvents) {
                if (afterRenderEvents.hasOwnProperty(property)) {
                    el = me[property];
                    if (el && el.on) {
                        Ext.each(afterRenderEvents[property], fn);
                    }
                }
            }
        }

        // This will add focus/blur listeners to the getFocusEl() element if that is naturally focusable.
        // If *not* naturally focusable, then the FocusManager must be enabled to get it to listen for focus so that
        // the FocusManager can track and highlight focus.
        me.addFocusListener();
    },

    /**
     * @private
     * <p>Sets up the focus listener on this Component's {@link #getFocusEl focusEl} if it has one.</p>
     * <p>Form Components which must implicitly participate in tabbing order usually have a naturally focusable
     * element as their {@link #getFocusEl focusEl}, and it is the DOM event of that recieving focus which drives
     * the Component's onFocus handling, and the DOM event of it being blurred which drives the onBlur handling.</p>
     * <p>If the {@link #getFocusEl focusEl} is <b>not</b> naturally focusable, then the listeners are only added
     * if the {@link Ext.FocusManager FocusManager} is enabled.</p>
     */
    addFocusListener: function() {
        var me = this,
            focusEl = me.getFocusEl(),
            needsTabIndex;

        // All Containers may be focusable, not only "form" type elements, but also
        // Panels, Toolbars, Windows etc.
        // Usually, the <DIV> element they will return as their focusEl will not be able to recieve focus
        // However, if the FocusManager is invoked, its non-default navigation handlers (invoked when
        // tabbing/arrowing off of certain Components) may explicitly focus a Panel or Container or FieldSet etc.
        // Add listeners to the focus and blur events on the focus element

        // If this Component returns a focusEl, we might need to add a focus listener to it.
        if (focusEl) {

            // getFocusEl might return a Component if a Container wishes to delegate focus to a descendant.
            // Window can do this via its defaultFocus configuration which can reference a Button.
            if (focusEl.isComponent) {
                return focusEl.addFocusListener();
            }

            // If the focusEl is naturally focusable, then we always need a focus listener to drive the Component's
            // onFocus handling.
            // If *not* naturally focusable, then we only need the focus listener if the FocusManager is enabled.
            needsTabIndex = focusEl.needsTabIndex();
            if (!me.focusListenerAdded && (!needsTabIndex || Ext.FocusManager.enabled)) {
                if (needsTabIndex) {
                    focusEl.dom.tabIndex = -1;
                }
                focusEl.on({
                    focus: me.onFocus,
                    blur: me.onBlur,
                    scope: me
                });
                me.focusListenerAdded = true;
            }
        }
    },

    /**
     * @private
     * <p>Returns the focus holder element associated with this Component. At the Component base class level, this function returns <code>undefined</code>.</p>
     * <p>Subclasses which use embedded focusable elements (such as Window, Field and Button) should override this for use by the {@link #focus} method.</p>
     * <p>Containers which need to participate in the {@link Ext.FocusManager FocusManager}'s navigation and Container focusing scheme also
     * need to return a focusEl, although focus is only listened for in this case if the {@link Ext.FocusManager FocusManager} is {@link Ext.FocusManager#method-enable enable}d.</p>
     * @returns {undefined} <code>undefined</code> because raw Components cannot by default hold focus.
     */
    getFocusEl: Ext.emptyFn,

    isFocusable: function(c) {
        var me = this,
            focusEl;
        if ((me.focusable !== false) && (focusEl = me.getFocusEl()) && me.rendered && !me.destroying && !me.isDestroyed && !me.disabled && me.isVisible(true)) {

            // getFocusEl might return a Component if a Container wishes to delegate focus to a descendant.
            // Window can do this via its defaultFocus configuration which can reference a Button.
            if (focusEl.isComponent) {
                return focusEl.isFocusable();
            }

            return focusEl && focusEl.dom && focusEl.isVisible();
        }
    },

    // private
    preFocus: Ext.emptyFn,

    // private
    onFocus: function(e) {
        var me = this,
            focusCls = me.focusCls,
            focusEl = me.getFocusEl();

        if (!me.disabled) {
            me.preFocus(e);
            if (focusCls && focusEl) {
                focusEl.addCls(me.addClsWithUI(focusCls, true));
            }
            if (!me.hasFocus) {
                me.hasFocus = true;
                me.fireEvent('focus', me, e);
            }
        }
    },

    // private
    beforeBlur : Ext.emptyFn,

    // private
    onBlur : function(e) {
        var me = this,
            focusCls = me.focusCls,
            focusEl = me.getFocusEl();

        if (me.destroying) {
            return;
        }

        me.beforeBlur(e);
        if (focusCls && focusEl) {
            focusEl.removeCls(me.removeClsWithUI(focusCls, true));
        }
        if (me.validateOnBlur) {
            me.validate();
        }
        me.hasFocus = false;
        me.fireEvent('blur', me, e);
        me.postBlur(e);
    },

    // private
    postBlur : Ext.emptyFn,

    /**
     * Tests whether this Component matches the selector string.
     * @param {String} selector The selector string to test against.
     * @return {Boolean} True if this Component matches the selector.
     */
    is: function(selector) {
        return Ext.ComponentQuery.is(this, selector);
    },

    /**
     * Walks up the `ownerCt` axis looking for an ancestor Container which matches the passed simple selector.
     *
     * Example:
     *
     *     var owningTabPanel = grid.up('tabpanel');
     *
     * @param {String} [selector] The simple selector to test.
     * @return {Ext.container.Container} The matching ancestor Container (or `undefined` if no match was found).
     */
    up: function(selector) {
        // Use bubble target to navigate upwards so that Components can implement their own hierarchy.
        // For example Menus implement getBubbleTarget because they have a parentMenu or ownerButton as an
        // upward link depending upon how they are owned and triggered.
        var result = this.getBubbleTarget();
        if (selector) {
            for (; result; result = result.getBubbleTarget()) {
                if (Ext.ComponentQuery.is(result, selector)) {
                    return result;
                }
            }
        }
        return result;
    },

    /**
     * Returns the next sibling of this Component.
     *
     * Optionally selects the next sibling which matches the passed {@link Ext.ComponentQuery ComponentQuery} selector.
     *
     * May also be refered to as **`next()`**
     *
     * Note that this is limited to siblings, and if no siblings of the item match, `null` is returned. Contrast with
     * {@link #nextNode}
     * @param {String} [selector] A {@link Ext.ComponentQuery ComponentQuery} selector to filter the following items.
     * @return {Ext.Component} The next sibling (or the next sibling which matches the selector).
     * Returns null if there is no matching sibling.
     */
    nextSibling: function(selector) {
        var o = this.ownerCt, it, last, idx, c;
        if (o) {
            it = o.items;
            idx = it.indexOf(this) + 1;
            if (idx) {
                if (selector) {
                    for (last = it.getCount(); idx < last; idx++) {
                        if ((c = it.getAt(idx)).is(selector)) {
                            return c;
                        }
                    }
                } else {
                    if (idx < it.getCount()) {
                        return it.getAt(idx);
                    }
                }
            }
        }
        return null;
    },

    /**
     * Returns the previous sibling of this Component.
     *
     * Optionally selects the previous sibling which matches the passed {@link Ext.ComponentQuery ComponentQuery}
     * selector.
     *
     * May also be refered to as **`prev()`**
     *
     * Note that this is limited to siblings, and if no siblings of the item match, `null` is returned. Contrast with
     * {@link #previousNode}
     * @param {String} [selector] A {@link Ext.ComponentQuery ComponentQuery} selector to filter the preceding items.
     * @return {Ext.Component} The previous sibling (or the previous sibling which matches the selector).
     * Returns null if there is no matching sibling.
     */
    previousSibling: function(selector) {
        var o = this.ownerCt, it, idx, c;
        if (o) {
            it = o.items;
            idx = it.indexOf(this);
            if (idx != -1) {
                if (selector) {
                    for (--idx; idx >= 0; idx--) {
                        if ((c = it.getAt(idx)).is(selector)) {
                            return c;
                        }
                    }
                } else {
                    if (idx) {
                        return it.getAt(--idx);
                    }
                }
            }
        }
        return null;
    },

    /**
     * Returns the previous node in the Component tree in tree traversal order.
     *
     * Note that this is not limited to siblings, and if invoked upon a node with no matching siblings, will walk the
     * tree in reverse order to attempt to find a match. Contrast with {@link #previousSibling}.
     * @param {String} [selector] A {@link Ext.ComponentQuery ComponentQuery} selector to filter the preceding nodes.
     * @return {Ext.Component} The previous node (or the previous node which matches the selector).
     * Returns null if there is no matching node.
     */
    previousNode: function(selector, includeSelf) {
        var node = this,
            result,
            it, len, i;

        // If asked to include self, test me
        if (includeSelf && node.is(selector)) {
            return node;
        }

        result = this.prev(selector);
        if (result) {
            return result;
        }

        if (node.ownerCt) {
            for (it = node.ownerCt.items.items, i = Ext.Array.indexOf(it, node) - 1; i > -1; i--) {
                if (it[i].query) {
                    result = it[i].query(selector);
                    result = result[result.length - 1];
                    if (result) {
                        return result;
                    }
                }
            }
            return node.ownerCt.previousNode(selector, true);
        }
    },

    /**
     * Returns the next node in the Component tree in tree traversal order.
     *
     * Note that this is not limited to siblings, and if invoked upon a node with no matching siblings, will walk the
     * tree to attempt to find a match. Contrast with {@link #nextSibling}.
     * @param {String} [selector] A {@link Ext.ComponentQuery ComponentQuery} selector to filter the following nodes.
     * @return {Ext.Component} The next node (or the next node which matches the selector).
     * Returns null if there is no matching node.
     */
    nextNode: function(selector, includeSelf) {
        var node = this,
            result,
            it, len, i;

        // If asked to include self, test me
        if (includeSelf && node.is(selector)) {
            return node;
        }

        result = this.next(selector);
        if (result) {
            return result;
        }

        if (node.ownerCt) {
            for (it = node.ownerCt.items, i = it.indexOf(node) + 1, it = it.items, len = it.length; i < len; i++) {
                if (it[i].down) {
                    result = it[i].down(selector);
                    if (result) {
                        return result;
                    }
                }
            }
            return node.ownerCt.nextNode(selector);
        }
    },

    /**
     * Retrieves the id of this component. Will autogenerate an id if one has not already been set.
     * @return {String}
     */
    getId : function() {
        return this.id || (this.id = 'ext-comp-' + (this.getAutoId()));
    },

    getItemId : function() {
        return this.itemId || this.id;
    },

    /**
     * Retrieves the top level element representing this component.
     * @return {Ext.dom.Element}
     */
    getEl : function() {
        return this.el;
    },

    /**
     * This is used to determine where to insert the 'html', 'contentEl' and 'items' in this component.
     * @private
     */
    getTargetEl: function() {
        return this.frameBody || this.el;
    },

    /**
     * @private
     * Returns the CSS style object which will set the Component's scroll styles. This must be applied
     * to the {@link #getTargetEl target element}.
     */
    getOverflowStyle: function() {
        var me = this,
            result = null;

        if (typeof me.autoScroll == 'boolean') {
            result = {
                overflow: me.autoScroll ? 'auto' : ''
            };
        } else if (me.overflowX !== undefined || me.overflowY !== undefined) {
            result = {
                'overflow-x':  (me.overflowX||''),
                'overflow-y':  (me.overflowY||'')
            };
        }

        // The scrollable container element must be non-statically positioned or IE6/7 will make
        // positioned children stay in place rather than scrolling with the rest of the content
        if (result && (Ext.isIE6 || Ext.isIE7)) {
            result.position = 'relative';
        }

        return result;
    },

    /**
     * Tests whether or not this Component is of a specific xtype. This can test whether this Component is descended
     * from the xtype (default) or whether it is directly of the xtype specified (shallow = true).
     *
     * **If using your own subclasses, be aware that a Component must register its own xtype to participate in
     * determination of inherited xtypes.**
     *
     * For a list of all available xtypes, see the {@link Ext.Component} header.
     *
     * Example usage:
     *
     *     var t = new Ext.form.field.Text();
     *     var isText = t.isXType('textfield');        // true
     *     var isBoxSubclass = t.isXType('field');       // true, descended from Ext.form.field.Base
     *     var isBoxInstance = t.isXType('field', true); // false, not a direct Ext.form.field.Base instance
     *
     * @param {String} xtype The xtype to check for this Component
     * @param {Boolean} [shallow=false] True to check whether this Component is directly of the specified xtype, false to
     * check whether this Component is descended from the xtype.
     * @return {Boolean} True if this component descends from the specified xtype, false otherwise.
     */
    isXType: function(xtype, shallow) {
        if (shallow) {
            return this.xtype === xtype;
        }
        else {
            return this.xtypesMap[xtype];
        }
    },

    /**
     * Returns this Component's xtype hierarchy as a slash-delimited string. For a list of all available xtypes, see the
     * {@link Ext.Component} header.
     *
     * **If using your own subclasses, be aware that a Component must register its own xtype to participate in
     * determination of inherited xtypes.**
     *
     * Example usage:
     *
     *     var t = new Ext.form.field.Text();
     *     alert(t.getXTypes());  // alerts 'component/field/textfield'
     *
     * @return {String} The xtype hierarchy string
     */
    getXTypes: function() {
        var self = this.self,
            xtypes, parentPrototype, parentXtypes;

        if (!self.xtypes) {
            xtypes = [];
            parentPrototype = this;

            while (parentPrototype) {
                parentXtypes = parentPrototype.xtypes;

                if (parentXtypes !== undefined) {
                    xtypes.unshift.apply(xtypes, parentXtypes);
                }

                parentPrototype = parentPrototype.superclass;
            }

            self.xtypeChain = xtypes;
            self.xtypes = xtypes.join('/');
        }

        return self.xtypes;
    },

    /**
     * Update the content area of a component.
     * @param {String/Object} htmlOrData If this component has been configured with a template via the tpl config then
     * it will use this argument as data to populate the template. If this component was not configured with a template,
     * the components content area will be updated via Ext.Element update
     * @param {Boolean} [loadScripts=false] Only legitimate when using the html configuration.
     * @param {Function} [callback] Only legitimate when using the html configuration. Callback to execute when
     * scripts have finished loading
     */
    update : function(htmlOrData, loadScripts, cb) {
        var me = this;

        if (me.tpl && !Ext.isString(htmlOrData)) {
            me.data = htmlOrData;
            if (me.rendered) {
                me.tpl[me.tplWriteMode](me.getTargetEl(), htmlOrData || {});
            }
        } else {
            me.html = Ext.isObject(htmlOrData) ? Ext.DomHelper.markup(htmlOrData) : htmlOrData;
            if (me.rendered) {
                me.getTargetEl().update(me.html, loadScripts, cb);
            }
        }

        if (me.rendered) {
            me.updateLayout();
        }
    },

    /**
     * Convenience function to hide or show this component by boolean.
     * @param {Boolean} visible True to show, false to hide
     * @return {Ext.Component} this
     */
    setVisible : function(visible) {
        return this[visible ? 'show': 'hide']();
    },

    /**
     * Returns true if this component is visible.
     *
     * @param {Boolean} [deep=false] Pass `true` to interrogate the visibility status of all parent Containers to
     * determine whether this Component is truly visible to the user.
     *
     * Generally, to determine whether a Component is hidden, the no argument form is needed. For example when creating
     * dynamically laid out UIs in a hidden Container before showing them.
     *
     * @return {Boolean} True if this component is visible, false otherwise.
     */
    isVisible: function(deep) {
        var me = this,
            child = me,
            visible = me.rendered && !me.hidden,
            ancestor = me.ownerCt;

        // Clear hiddenOwnerCt property
        me.hiddenAncestor = false;
        if (me.destroyed) {
            return false;
        }

        if (deep && visible && ancestor) {
            while (ancestor) {
                // If any ancestor is hidden, then this is hidden.
                // If an ancestor Panel (only Panels have a collapse method) is collapsed,
                // then its layoutTarget (body) is hidden, so this is hidden unless its within a
                // docked item; they are still visible when collapsed (Unless they themseves are hidden)
                if (ancestor.hidden || (ancestor.collapsed &&
                        !(ancestor.getDockedItems && Ext.Array.contains(ancestor.getDockedItems(), child)))) {
                    // Store hiddenOwnerCt property if needed
                    me.hiddenAncestor = ancestor;
                    visible = false;
                    break;
                }
                child = ancestor;
                ancestor = ancestor.ownerCt;
            }
        }
        return visible;
    },

    onBoxReady: function(){
        var me = this;

        if (me.disableOnBoxReady) {
            me.onDisable();
        } else if (me.enableOnBoxReady) {
            me.onEnable();
        }
    },

    /**
     * Enable the component
     * @param {Boolean} [silent=false] Passing true will supress the 'enable' event from being fired.
     */
    enable: function(silent) {
        var me = this;

        delete me.disableOnBoxReady;
        me.removeCls(me.disabledCls);
        if (me.rendered) {
            me.onEnable();
        } else {
            me.enableOnBoxReady = true;
        }

        me.disabled = false;
        delete me.resetDisable;

        if (silent !== true) {
            me.fireEvent('enable', me);
        }

        return me;
    },

    /**
     * Disable the component.
     * @param {Boolean} [silent=false] Passing true will supress the 'disable' event from being fired.
     */
    disable: function(silent) {
        var me = this;

        delete me.enableOnBoxReady;
        me.addCls(me.disabledCls);
        if (me.rendered) {
            me.onDisable();
        } else {
            me.disableOnBoxReady = true;
        }

        me.disabled = true;

        if (silent !== true) {
            delete me.resetDisable;
            me.fireEvent('disable', me);
        }

        return me;
    },

    /**
     * Allows addition of behavior to the enable operation.
     * After calling the superclass???s onEnable, the Component will be enabled.
     *
     * @template
     * @protected
     */
    onEnable: function() {
        if (this.maskOnDisable) {
            this.el.dom.disabled = false;
            this.unmask();
        }
    },

    /**
     * Allows addition of behavior to the disable operation.
     * After calling the superclass???s onDisable, the Component will be disabled.
     *
     * @template
     * @protected
     */
    onDisable : function() {
        if (this.maskOnDisable) {
            this.el.dom.disabled = true;
            this.mask();
        }
    },

    mask: function() {
        var box = this.lastBox,
            target = this.getMaskTarget(),
            args = [];

        // Pass it the height of our element if we know it.
        if (box) {
            args[2] = box.height;
        }
        target.mask.apply(target, args);
    },

    unmask: function() {
        this.getMaskTarget().unmask();
    },

    getMaskTarget: function(){
        return this.el;
    },

    /**
     * Method to determine whether this Component is currently disabled.
     * @return {Boolean} the disabled state of this Component.
     */
    isDisabled : function() {
        return this.disabled;
    },

    /**
     * Enable or disable the component.
     * @param {Boolean} disabled True to disable.
     */
    setDisabled : function(disabled) {
        return this[disabled ? 'disable': 'enable']();
    },

    /**
     * Method to determine whether this Component is currently set to hidden.
     * @return {Boolean} the hidden state of this Component.
     */
    isHidden : function() {
        return this.hidden;
    },

    /**
     * Adds a CSS class to the top level element representing this component.
     * @param {String/String[]} cls The CSS class name to add
     * @return {Ext.Component} Returns the Component to allow method chaining.
     */
    addCls : function(cls) {
        var me = this,
            el = me.rendered ? me.el : me.protoEl;

        el.addCls.apply(el, arguments);
        return me;
    },

    /**
     * @inheritdoc Ext.AbstractComponent#addCls
     * @deprecated 4.1 Use {@link #addCls} instead.
     */
    addClass : function() {
        return this.addCls.apply(this, arguments);
    },

    /**
     * Checks if the specified CSS class exists on this element's DOM node.
     * @param {String} className The CSS class to check for
     * @return {Boolean} True if the class exists, else false
     * @method
     */
    hasCls: function (cls) {
        var me = this,
            el = me.rendered ? me.el : me.protoEl;

        return el.hasCls.apply(el, arguments);
    },

    /**
     * Removes a CSS class from the top level element representing this component.
     * @param {String/String[]} cls The CSS class name to remove
     * @returns {Ext.Component} Returns the Component to allow method chaining.
     */
    removeCls : function(cls) {
        var me = this,
            el = me.rendered ? me.el : me.protoEl;

        el.removeCls.apply(el, arguments);
        return me;
    },

    //<debug>
    removeClass : function() {
        if (Ext.isDefined(Ext.global.console)) {
            Ext.global.console.warn('Ext.Component: removeClass has been deprecated. Please use removeCls.');
        }
        return this.removeCls.apply(this, arguments);
    },
    //</debug>

    addOverCls: function() {
        var me = this;
        if (!me.disabled) {
            me.el.addCls(me.overCls);
        }
    },

    removeOverCls: function() {
        this.el.removeCls(this.overCls);
    },

    addListener : function(element, listeners, scope, options) {
        var me = this,
            fn,
            option;

        if (Ext.isString(element) && (Ext.isObject(listeners) || options && options.element)) {
            if (options.element) {
                fn = listeners;

                listeners = {};
                listeners[element] = fn;
                element = options.element;
                if (scope) {
                    listeners.scope = scope;
                }

                for (option in options) {
                    if (options.hasOwnProperty(option)) {
                        if (me.eventOptionsRe.test(option)) {
                            listeners[option] = options[option];
                        }
                    }
                }
            }

            // At this point we have a variable called element,
            // and a listeners object that can be passed to on
            if (me[element] && me[element].on) {
                me.mon(me[element], listeners);
            } else {
                me.afterRenderEvents = me.afterRenderEvents || {};
                if (!me.afterRenderEvents[element]) {
                    me.afterRenderEvents[element] = [];
                }
                me.afterRenderEvents[element].push(listeners);
            }
        }

        return me.mixins.observable.addListener.apply(me, arguments);
    },

    // inherit docs
    removeManagedListenerItem: function(isClear, managedListener, item, ename, fn, scope){
        var me = this,
            element = managedListener.options ? managedListener.options.element : null;

        if (element) {
            element = me[element];
            if (element && element.un) {
                if (isClear || (managedListener.item === item && managedListener.ename === ename && (!fn || managedListener.fn === fn) && (!scope || managedListener.scope === scope))) {
                    element.un(managedListener.ename, managedListener.fn, managedListener.scope);
                    if (!isClear) {
                        Ext.Array.remove(me.managedListeners, managedListener);
                    }
                }
            }
        } else {
            return me.mixins.observable.removeManagedListenerItem.apply(me, arguments);
        }
    },

    /**
     * Provides the link for Observable's fireEvent method to bubble up the ownership hierarchy.
     * @return {Ext.container.Container} the Container which owns this Component.
     */
    getBubbleTarget : function() {
        return this.ownerCt;
    },

    /**
     * Method to determine whether this Component is floating.
     * @return {Boolean} the floating state of this component.
     */
    isFloating : function() {
        return this.floating;
    },

    /**
     * Method to determine whether this Component is draggable.
     * @return {Boolean} the draggable state of this component.
     */
    isDraggable : function() {
        return !!this.draggable;
    },

    /**
     * Method to determine whether this Component is droppable.
     * @return {Boolean} the droppable state of this component.
     */
    isDroppable : function() {
        return !!this.droppable;
    },

    /**
     * Method to manage awareness of when components are added to their
     * respective Container, firing an #added event. References are
     * established at add time rather than at render time.
     *
     * Allows addition of behavior when a Component is added to a
     * Container. At this stage, the Component is in the parent
     * Container's collection of child items. After calling the
     * superclass's onAdded, the ownerCt reference will be present,
     * and if configured with a ref, the refOwner will be set.
     *
     * @param {Ext.container.Container} container Container which holds the component
     * @param {Number} pos Position at which the component was added
     *
     * @template
     * @protected
     */
    onAdded : function(container, pos) {
        var me = this;
        me.ownerCt = container;
        if (me.hasListeners.added) {
            me.fireEvent('added', me, container, pos);
        }
    },

    /**
     * Method to manage awareness of when components are removed from their
     * respective Container, firing a #removed event. References are properly
     * cleaned up after removing a component from its owning container.
     *
     * Allows addition of behavior when a Component is removed from
     * its parent Container. At this stage, the Component has been
     * removed from its parent Container's collection of child items,
     * but has not been destroyed (It will be destroyed if the parent
     * Container's autoDestroy is true, or if the remove call was
     * passed a truthy second parameter). After calling the
     * superclass's onRemoved, the ownerCt and the refOwner will not
     * be present.
     * @param {Boolean} destroying Will be passed as true if the Container performing the remove operation will delete this
     * Component upon remove.
     *
     * @template
     * @protected
     */
    onRemoved : function(destroying) {
        var me = this;
        if (me.hasListeners.removed) {
            me.fireEvent('removed', me, me.ownerCt);
        }
        delete me.ownerCt;
    },

    /**
     * Invoked before the Component is destroyed.
     *
     * @method
     * @template
     * @protected
     */
    beforeDestroy : Ext.emptyFn,

    /**
     * Allows addition of behavior to the resize operation.
     *
     * Called when Ext.resizer.Resizer#drag event is fired.
     *
     * @method
     * @template
     * @protected
     */
    onResize : Ext.emptyFn,

    /**
     * Sets the width and height of this Component. This method fires the {@link #resize} event. This method can accept
     * either width and height as separate arguments, or you can pass a size object like `{width:10, height:20}`.
     *
     * @param {Number/String/Object} width The new width to set. This may be one of:
     *
     *   - A Number specifying the new width in the {@link #getEl Element}'s {@link Ext.Element#defaultUnit}s (by default, pixels).
     *   - A String used to set the CSS width style.
     *   - A size object in the format `{width: widthValue, height: heightValue}`.
     *   - `undefined` to leave the width unchanged.
     *
     * @param {Number/String} height The new height to set (not required if a size object is passed as the first arg).
     * This may be one of:
     *
     *   - A Number specifying the new height in the {@link #getEl Element}'s {@link Ext.Element#defaultUnit}s (by default, pixels).
     *   - A String used to set the CSS height style. Animation may **not** be used.
     *   - `undefined` to leave the height unchanged.
     *
     * @return {Ext.Component} this
     */
    setSize : function(width, height) {
        var me = this;

        // support for standard size objects
        if (width && typeof width == 'object') {
            height = width.height;
            width  = width.width;
        }

        // Constrain within configured maxima
        if (typeof width == 'number') {
            me.width = Ext.Number.constrain(width, me.minWidth, me.maxWidth);
        } else if (width === null) {
            delete me.width;
        }
        
        if (typeof height == 'number') {
            me.height = Ext.Number.constrain(height, me.minHeight, me.maxHeight);
        } else if (height === null) {
            delete me.height;
        }

        // If not rendered, all we need to is set the properties.
        // The initial layout will set the size
        if (me.rendered && me.isVisible()) {

            // If we are changing size, then we are not the root.
            me.updateLayout({
                isRoot: false
            });
        }

        return me;
    },

    /**
     * Determines whether this Component is the root of a layout. This returns `true` if
     * this component can run its layout without assistance from or impact on its owner.
     * If this component cannot run its layout given these restrictions, `false` is returned
     * and its owner will be considered as the next candidate for the layout root.
     *
     * Setting the {@link #_isLayoutRoot} property to `true` causes this method to always
     * return `true`. This may be useful when updating a layout of a Container which shrink
     * wraps content, and you know that it will not change size, and so can safely be the
     * topmost participant in the layout run.
     * @protected
     */
    isLayoutRoot: function() {
        var me = this,
            ownerLayout = me.ownerLayout;

        // Return true if we have been explicitly flagged as the layout root, or if we are floating.
        // Sometimes floating Components get an ownerCt ref injected into them which is *not* a true ownerCt, merely
        // an upward link for reference purposes. For example a grid column menu is linked to the
        // owning header via an ownerCt reference.
        if (!ownerLayout || me._isLayoutRoot || me.floating) {
            return true;
        }

        return ownerLayout.isItemLayoutRoot(me);
    },

    /**
     * Returns true if layout is suspended for this component. This can come from direct
     * suspension of this component's layout activity ({@link #suspendLayouts}) or if one
     * of this component's containers is suspended.
     *
     * @return {Boolean} True layout of this component is suspended.
     */
    isLayoutSuspended: function () {
        var comp = this,
            ownerLayout;

        while (comp) {
            if (comp.layoutSuspendCount || comp.suspendLayout) {
                return true;
            }

            ownerLayout = comp.ownerLayout;
            if (!ownerLayout) {
                break;
            }

            // TODO - what about suspending a Layout instance?

            // this works better than ownerCt since ownerLayout means "is managed by" in
            // the proper sense... some floating components have ownerCt but won't have an
            // ownerLayout
            comp = ownerLayout.owner;
        }

        return false;
    },

    /**
     * Updates this component's layout. If this update effects this components {@link #ownerCt},
     * that component's `updateLayout` method will be called to perform the layout instead.
     * Otherwise, just this component (and its child items) will layout.
     *
     * @param {Object} options An object with layout options.
     * @param {Boolean} options.defer True if this layout should be deferred.
     * @param {Boolean} options.isRoot True if this layout should be the root of the layout.
     */
    updateLayout: function (options) {
        var me = this,
            defer,
            isRoot = options && options.isRoot;

        if (!me.rendered || me.layoutSuspendCount || me.suspendLayout) {
            return;
        }

        if (me.hidden) {
            Ext.AbstractComponent.cancelLayout(me);
        } else if (typeof isRoot != 'boolean') {
            isRoot = me.isLayoutRoot();
        }

        // if we aren't the root, see if our ownerLayout will handle it...
        if (isRoot || !me.ownerLayout || !me.ownerLayout.onContentChange(me)) {
            // either we are the root or our ownerLayout doesn't care
            if (!me.isLayoutSuspended()) {
                // we aren't suspended (knew that), but neither is any of our ownerCt's...
                defer = (options && options.hasOwnProperty('defer')) ? options.defer : me.deferLayouts;
                Ext.AbstractComponent.updateLayout(me, defer);
            }
        }
    },

    /**
     * Returns an object that describes how this component's width and height is managed. These
     * objects are shared and should not be modified.
     *
     * @return {Object} The size model for this component.
     * @return {Object} return.width The width aspect of this component's size model.
     * @return {Boolean} return.width.auto True if width is either natural or shrinkWrap (not fixed).
     * @return {Boolean} return.width.calculated True if width is calculated by a layout.
     * @return {Boolean} return.width.configured True if width is specified on this component.
     * @return {Boolean} return.width.fixed True if width is either calculated or configured.
     * @return {Boolean} return.width.natural True if width is determined by CSS and does not depend on content.
     * @return {Boolean} return.width.shrinkWrap True if width is determined by content.
     * @return {Object} return.height The height aspect of this component's size model.
     * @return {Boolean} return.height.auto True if height is either natural or shrinkWrap (not fixed).
     * @return {Boolean} return.height.calculated True if height is calculated by a layout.
     * @return {Boolean} return.height.configured True if height is specified on this component.
     * @return {Boolean} return.height.fixed True if height is either calculated or configured.
     * @return {Boolean} return.height.natural True if height is determined by CSS and does not depend on content.
     * @return {Boolean} return.height.shrinkWrap True if height is determined by content.
     */
    getSizeModel: function (ownerCtSizeModel) {
        var me = this,
            Layout = Ext.layout.Layout.prototype,
            models = Layout.sizeModels,
            heightModel, ownerLayout, policy, shrinkWrap, widthModel,
            ownerCtx = me.componentLayout.ownerContext;

        if (ownerCtx) {
            widthModel = ownerCtx.widthModel;
            heightModel = ownerCtx.heightModel;
        } else {
            if (typeof me.width == 'number') {
                widthModel = models.configured;
            }
            if (typeof me.height == 'number') {
                heightModel = models.configured;
            }
        }

        if (!widthModel || !heightModel) {
            if (me.floating) {
                policy = Layout.autoSizePolicy;
                shrinkWrap = 3;
            } else {
                if (!(ownerLayout = me.ownerLayout)) {
                    policy = Layout.autoSizePolicy;
                    shrinkWrap = me.shrinkWrap;
                } else {
                    policy = ownerLayout.getItemSizePolicy(me);
                    shrinkWrap = ownerLayout.isItemShrinkWrap(me);
                }

                shrinkWrap = (shrinkWrap === true) ? 3 : (shrinkWrap || 0); // false->0, true->3

                if (shrinkWrap !== 3) {
                    if (!ownerCtSizeModel) {
                        ownerCtSizeModel = me.ownerCt && me.ownerCt.getSizeModel();
                    }
                    if (ownerCtSizeModel) {
                        shrinkWrap |= (ownerCtSizeModel.width.shrinkWrap ? 1 : 0) | (ownerCtSizeModel.height.shrinkWrap ? 2 : 0);
                    }
                }
            }

            if (!widthModel) {
                if (!policy.setsWidth) {
                    widthModel = (shrinkWrap & 1) ? models.shrinkWrap : models.natural;
                } else if (policy.readsWidth) {
                    widthModel = (shrinkWrap & 1) ? models.calculatedFromShrinkWrap :
                                    models.calculatedFromNatural;
                } else {
                    widthModel = models.calculated;
                }
            }

            if (!heightModel) {
                if (!policy.setsHeight) {
                    heightModel = (shrinkWrap & 2) ? models.shrinkWrap : models.natural;
                } else if (policy.readsHeight) {
                    heightModel = (shrinkWrap & 2) ? models.calculatedFromShrinkWrap :
                                    models.calculatedFromNatural;
                } else {
                    heightModel = models.calculated;
                }
            }
        }

        return {
            width: widthModel,
            height: heightModel
        };
    },

    isDescendant: function(ancestor) {
        if (ancestor.isContainer) {
            for (var c = this.ownerCt; c; c = c.ownerCt) {
                if (c === ancestor) {
                    return true;
                }
            }
        }
        return false;
    },

    /**
     * This method needs to be called whenever you change something on this component that requires the Component's
     * layout to be recalculated.
     * @return {Ext.container.Container} this
     */
    doComponentLayout : function() {
        this.updateLayout();
        return this;
    },

    /**
     * Forces this component to redo its componentLayout.
     * @deprecated 4.1.0 Use {@link #updateLayout} instead.
     */
    forceComponentLayout: function () {
        this.updateLayout();
    },

    // @private
    setComponentLayout : function(layout) {
        var currentLayout = this.componentLayout;
        if (currentLayout && currentLayout.isLayout && currentLayout != layout) {
            currentLayout.setOwner(null);
        }
        this.componentLayout = layout;
        layout.setOwner(this);
    },

    getComponentLayout : function() {
        var me = this;

        if (!me.componentLayout || !me.componentLayout.isLayout) {
            me.setComponentLayout(Ext.layout.Layout.create(me.componentLayout, 'autocomponent'));
        }
        return me.componentLayout;
    },

    /**
     * Called by the layout system after the Component has been layed out.
     *
     * @param {Number} width The width that was set
     * @param {Number} height The height that was set
     * @param {Number} oldWidth The old width. <code>undefined</code> if this was the initial layout.
     * @param {Number} oldHeight The old height. <code>undefined</code> if this was the initial layout.
     *
     * @template
     * @protected
     */
    afterComponentLayout: function(width, height, oldWidth, oldHeight) {
        var me = this;
        if (++me.componentLayoutCounter === 1) {
            me.afterFirstLayout(width, height);
        }
        if (me.hasListeners.resize && (width !== oldWidth || height !== oldHeight)) {
            me.fireEvent('resize', me, width, height, oldWidth, oldHeight);
        }
    },

    /**
     * Occurs before componentLayout is run. Returning false from this method will prevent the componentLayout from
     * being executed.
     *
     * @param {Number} adjWidth The box-adjusted width that was set
     * @param {Number} adjHeight The box-adjusted height that was set
     *
     * @template
     * @protected
     */
    beforeComponentLayout: function(width, height) {
        return true;
    },

    /**
     * Sets the left and top of the component. To set the page XY position instead, use {@link #setPagePosition}. This
     * method fires the {@link #move} event.
     * @param {Number} left The new left
     * @param {Number} top The new top
     * @param {Boolean/Object} [animate] If true, the Component is _animated_ into its new position. You may also pass an
     * animation configuration.
     * @return {Ext.Component} this
     */
    setPosition : function(x, y, animate) {
        var me = this,
            pos = me.beforeSetPosition.apply(me, arguments);

        if (pos && me.rendered) {
            // Convert position WRT RTL
            pos = me.convertPosition(pos);

            if (animate) {
                me.stopAnimation();
                me.animate(Ext.apply({
                    duration: 1000,
                    listeners: {
                        afteranimate: Ext.Function.bind(me.afterSetPosition, me, [pos.left, pos.top])
                    },
                    to: pos
                }, animate));
            } else {
                // Must use Element's methods to set element position because, if it is a Layer (floater), it may need to sync a shadow
                // We must also only set the properties which are defined because Element.setLeftTop autos any undefined coordinates
                if (pos.left !== undefined && pos.top !== undefined) {
                    me.el.setLeftTop(pos.left, pos.top);
                } else if (pos.left !== undefined) {
                    me.el.setLeft(pos.left);
                } else if (pos.top !==undefined) {
                    me.el.setTop(pos.top);
                }
                me.afterSetPosition(pos.left, pos.top);
            }
        }
        return me;
    },

    /**
     * @private Template method called before a Component is positioned.
     */
    beforeSetPosition: function (x, y, animate) {
        var pos, x0;

        // decode the position arguments:
        if (!x || Ext.isNumber(x)) {
            pos = { x: x, y : y, anim: animate };
        } else if (Ext.isNumber(x0 = x[0])) { // an array of [x, y]
            pos = { x : x0, y : x[1], anim: y };
        } else {
            pos = { x: x.x, y: x.y, anim: y }; // already an object w/ x & y properties
        }

        pos.hasX = Ext.isNumber(pos.x);
        pos.hasY = Ext.isNumber(pos.y);

        // store the position as specified:
        this.x = pos.x;
        this.y = pos.y;

        return (pos.hasX || pos.hasY) ? pos : null;
    },

    /**
     * Template method called after a Component has been positioned.
     *
     * @param {Number} x
     * @param {Number} y
     *
     * @template
     * @protected
     */
    afterSetPosition: function(x, y) {
        var me = this;
        me.onPosition(x, y);
        if (me.hasListeners.move) {
            me.fireEvent('move', me, x, y);
        }
    },

    /**
     * This method converts an "{x: x, y: y}" object to a "{left: x+'px', top: y+'px'}" object.
     * The returned object contains the styles to set to effect the position. This is
     * overridden in RTL mode to be "{right: x, top: y}".
     * @private
     */
    convertPosition: function (pos, withUnits) {
        var ret = {},
            El = Ext.Element;

        if (pos.hasX) {
            ret.left = withUnits ? El.addUnits(pos.x) : pos.x;
        }
        if (pos.hasY) {
            ret.top = withUnits ? El.addUnits(pos.y) : pos.y;
        }

        return ret;
    },

    /**
     * Called after the component is moved, this method is empty by default but can be implemented by any
     * subclass that needs to perform custom logic after a move occurs.
     *
     * @param {Number} x The new x position
     * @param {Number} y The new y position
     *
     * @template
     * @protected
     */
    onPosition: Ext.emptyFn,

    /**
     * Sets the width of the component. This method fires the {@link #resize} event.
     *
     * @param {Number} width The new width to setThis may be one of:
     *
     *   - A Number specifying the new width in the {@link #getEl Element}'s {@link Ext.Element#defaultUnit}s (by default, pixels).
     *   - A String used to set the CSS width style.
     *
     * @return {Ext.Component} this
     */
    setWidth : function(width) {
        return this.setSize(width);
    },

    /**
     * Sets the height of the component. This method fires the {@link #resize} event.
     *
     * @param {Number} height The new height to set. This may be one of:
     *
     *   - A Number specifying the new height in the {@link #getEl Element}'s {@link Ext.Element#defaultUnit}s (by default, pixels).
     *   - A String used to set the CSS height style.
     *   - _undefined_ to leave the height unchanged.
     *
     * @return {Ext.Component} this
     */
    setHeight : function(height) {
        return this.setSize(undefined, height);
    },

    /**
     * Gets the current size of the component's underlying element.
     * @return {Object} An object containing the element's size {width: (element width), height: (element height)}
     */
    getSize : function() {
        return this.el.getSize();
    },

    /**
     * Gets the current width of the component's underlying element.
     * @return {Number}
     */
    getWidth : function() {
        return this.el.getWidth();
    },

    /**
     * Gets the current height of the component's underlying element.
     * @return {Number}
     */
    getHeight : function() {
        return this.el.getHeight();
    },

    /**
     * Gets the {@link Ext.ComponentLoader} for this Component.
     * @return {Ext.ComponentLoader} The loader instance, null if it doesn't exist.
     */
    getLoader: function(){
        var me = this,
            autoLoad = me.autoLoad ? (Ext.isObject(me.autoLoad) ? me.autoLoad : {url: me.autoLoad}) : null,
            loader = me.loader || autoLoad;

        if (loader) {
            if (!loader.isLoader) {
                me.loader = new Ext.ComponentLoader(Ext.apply({
                    target: me,
                    autoLoad: autoLoad
                }, loader));
            } else {
                loader.setTarget(me);
            }
            return me.loader;

        }
        return null;
    },

    /**
     * Sets the dock position of this component in its parent panel. Note that this only has effect if this item is part
     * of the dockedItems collection of a parent that has a DockLayout (note that any Panel has a DockLayout by default)
     * @param {Object} dock The dock position.
     * @param {Boolean} [layoutParent=false] True to re-layout parent.
     * @return {Ext.Component} this
     */
    setDocked : function(dock, layoutParent) {
        var me = this;

        me.dock = dock;
        if (layoutParent && me.ownerCt && me.rendered) {
            me.ownerCt.updateLayout();
        }
        return me;
    },

    /**
     *
     * @param {String/Number} border The border, see {@link #border}. If a falsey value is passed
     * the border will be removed.
     */
    setBorder: function(border, /* private */ targetEl) {
        var me = this,
            initial = !!targetEl;

        if (me.rendered || initial) {
            if (!initial) {
                targetEl = me.el;
            }

            if (!border) {
                border = 0;
            } else {
                border = Ext.Element.unitizeBox((border === true) ? 1 : border);
            }
            targetEl.setStyle('border-width', border);
            if (!initial) {
                me.updateLayout();
            }
        }
        me.border = border;
    },

    onDestroy : function() {
        var me = this;

        if (me.monitorResize && Ext.EventManager.resizeEvent) {
            Ext.EventManager.resizeEvent.removeListener(me.setSize, me);
        }

        // Destroying the floatingItems ZIndexManager will also destroy descendant floating Components
        Ext.destroy(
            me.componentLayout,
            me.loadMask,
            me.floatingItems
        );
    },

    /**
     * Destroys the Component.
     */
    destroy : function() {
        var me = this,
            selectors = me.renderSelectors,
            selector,
            el;

        if (!me.isDestroyed) {
            if (!me.hasListeners.beforedestroy || me.fireEvent('beforedestroy', me) !== false) {
                me.destroying = true;
                me.beforeDestroy();

                if (me.floating) {
                    delete me.floatParent;
                    // A zIndexManager is stamped into a *floating* Component when it is added to a Container.
                    // If it has no zIndexManager at render time, it is assigned to the global Ext.WindowManager instance.
                    if (me.zIndexManager) {
                        me.zIndexManager.unregister(me);
                    }
                } else if (me.ownerCt && me.ownerCt.remove) {
                    me.ownerCt.remove(me, false);
                }

                me.onDestroy();

                // Attempt to destroy all plugins
                Ext.destroy(me.plugins);

                if (me.hasListeners.destroy) {
                    me.fireEvent('destroy', me);
                }
                Ext.ComponentManager.unregister(me);

                me.mixins.state.destroy.call(me);

                me.clearListeners();
                // make sure we clean up the element references after removing all events
                if (me.rendered) {
                    // In case we are queued for a layout.
                    Ext.AbstractComponent.cancelLayout(me);

                    if (!me.preserveElOnDestroy) {
                        me.el.remove();
                    }
                    me.mixins.elementCt.destroy.call(me); // removes childEls
                    if (selectors) {
                        for (selector in selectors) {
                            if (selectors.hasOwnProperty(selector)) {
                                el = me[selector];
                                if (el) { // in case any other code may have already removed it
                                    delete me[selector];
                                    el.remove();
                                }
                            }
                        }
                    }

                    delete me.el;
                    delete me.frameBody;
                    delete me.rendered;
                }

                me.destroying = false;
                me.isDestroyed = true;
            }
        }
    },

    /**
     * Retrieves a plugin by its pluginId which has been bound to this component.
     * @param {Object} pluginId
     * @return {Ext.AbstractPlugin} plugin instance.
     */
    getPlugin: function(pluginId) {
        var i = 0,
            plugins = this.plugins,
            ln = plugins.length;
        for (; i < ln; i++) {
            if (plugins[i].pluginId === pluginId) {
                return plugins[i];
            }
        }
    },

    /**
     * Determines whether this component is the descendant of a particular container.
     * @param {Ext.Container} container
     * @return {Boolean} True if it is.
     */
    isDescendantOf: function(container) {
        return !!this.findParentBy(function(p){
            return p === container;
        });
    }
}, function() {
    var abstractComponent = this;

    abstractComponent.createAlias({
        on: 'addListener',
        prev: 'previousSibling',
        next: 'nextSibling'
    });

    Ext.resumeLayouts = function (flush) {
        abstractComponent.resumeLayouts(flush);
    };

    Ext.suspendLayouts = function () {
        abstractComponent.suspendLayouts();
    };

    /**
     *
     * Utility wrapper that suspends layouts of all components for the duration of a given function.
     * @param {Function} fn The function to execute.
     * @param {Object} scope (Optional) The scope (`this` reference) in which the specified function is executed.
     */
    Ext.batchLayouts = function(fn, scope) {
        abstractComponent.suspendLayouts();
        // Invoke the function
        fn.call(scope);
        abstractComponent.resumeLayouts(true);
    };
});
