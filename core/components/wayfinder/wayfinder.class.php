<?php
/**
 * Wayfinder Class
 *
 * @package wayfinder
 */
class Wayfinder {
    /**
     * The array of config parameters
     * @access private
     * @var array $_config
     */
    public $_config;
    public $_templates;
    public $_css;
    public $modx = null;
    public $docs = array ();
    public $parentTree = array ();
    public $hasChildren = array ();
    public $placeHolders = array (
        'wrapperLevel' => array (
            '[[+wf.wrapper]]',
            '[[+wf.classes]]',
            '[[+wf.classnames]]'
        ),
        'tvs' => array (),

    );
    public $tvList = array ();
    public $debugInfo = array ();
    private $_cached = false;
    private $_cachedTVs = array();
    private $_cacheKeys = array();
    private $_cacheOptions = array();

    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        $this->_config = array_merge(array(
            'id' => $this->modx->resource->get('id'),
            'level' => 0,
            'includeDocs' => '',
            'excludeDocs' => '',
            'ph' => false,
            'debug' => false,
            'ignoreHidden' =>false,
            'hideSubMenus' => false,
            'useWeblinkUrl' => true,
            'fullLink' => false,
            'sortOrder' => 'ASC',
            'sortBy' => 'menuindex',
            'limit' => 0,
            'cssTpl' => false,
            'jsTpl' => false,
            'rowIdPrefix' => false,
            'textOfLinks' => 'menutitle',
            'titleOfLinks' => 'pagetitle',
            'displayStart' => false,
            'permissions' => 'list',
            'previewUnpublished' => false,
        ),$config);
        if (empty($this->_config['hereId'])) {
            $this->_config['hereId'] = $this->modx->resource->get('id');
        }

        if (isset($config['sortOrder'])) {
            $this->_config['sortOrder'] = strtoupper($config['sortOrder']);
        }
        if (isset($config['startId'])) { $this->_config['id'] = $config['startId']; }
        if (isset($config['removeNewLines'])) { $this->_config['nl'] = ''; }
        
        if (isset($this->_config['contexts'])) {
            $this->_config['contexts'] = preg_replace('/,  +/', ',', $this->_config['contexts']);
        }
    }


    /**
     * Main entry point to generate the menu
     *
     * @return string The menu HTML or relevant error message.
     */
    public function run() {
        /* setup here checking array */
        $this->parentTree = $this->modx->getParentIds($this->_config['hereId']);
        $this->parentTree[] = $this->_config['hereId'];

        if (!empty($this->_config['debug'])) {
            $this->addDebugInfo('settings', 'Settings', 'Settings', 'Settings used to create this menu.', $this->_config);
            $this->addDebugInfo('settings', 'CSS', 'CSS Settings', 'Available CSS options.', $this->_css);
        }
        /* load the templates */
        $this->checkTemplates();

        /* register any scripts */
        if ($this->_config['cssTpl'] || $this->_config['jsTpl']) {
            $this->regJsCss();
        }
        /* check for cached files */
        $cacheResults = $this->modx->getOption('cacheResults',$this->_config,true);
        if ($cacheResults) {
            $this->modx->getCacheManager();
            $cache = $this->getFromCache();
            if (!empty($cache) && !empty($cache['docs']) && !empty($cache['children'])) {
                /* cache files are set */
                $this->docs = $cache['docs'];
                $this->hasChildren = $cache['children'];
                $this->_cached = true;
            }
        }
        if (empty($this->_cached)) {
            /* cache files not set - get all of the resources */
            $this->docs = $this->getData();
            /* set cache files */
            if ($cacheResults) {
                $this->setToCache();
            }
        }
        if (!empty($this->docs)) {
            /* sort resources by level for proper wrapper substitution */
            ksort($this->docs);
            /* build the menu */
            return $this->buildMenu();
        } else {
            $noneReturn = $this->_config['debug'] ? '<p>No resources found for menu.</p>' : '';
            return $noneReturn;
        }
    }

    /**
     * Attempt to get the result set from the cache
     * 
     * @return array Cached result set, if existent
     */
    public function getFromCache() {
        $cacheKeys = $this->getCacheKeys();
        /* check for cache */
        $cache = array();
        $cache['docs'] = $this->modx->cacheManager->get($cacheKeys['docs'],$this->_cacheOptions);
        $cache['children'] = $this->modx->cacheManager->get($cacheKeys['children'],$this->_cacheOptions);
        return $cache;
    }

    /**
     * Set result-set data to cache
     * @return boolean
     */
    public function setToCache() {
        $cacheKeys = $this->getCacheKeys();
        $cacheTime = $this->modx->getOption('cacheTime',$this->_config,3600);
        $this->modx->cacheManager->set($cacheKeys['docs'],$this->docs,$cacheTime,$this->_cacheOptions);
        $this->modx->cacheManager->set($cacheKeys['children'],$this->hasChildren,$cacheTime,$this->_cacheOptions);
        return true;
    }

    /**
     * Generate an array of cache keys used by wayfinder caching
     * @return array An array of cache keys
     */
    public function getCacheKeys() {
        if (!empty($this->_cacheKeys)) return $this->_cacheKeys;
        
        /* generate a UID based on the params passed to Wayfinder and the resource ID
         * and the User ID (so that permissions get correctly applied) */
        $cacheKey = 'wf-'.$this->modx->user->get('id').'-'.base64_encode(serialize($this->_config));
        $childrenCacheKey = $cacheKey.'-children';

        /* set cache keys to proper Resource cache so will sync with MODX core caching */
        $this->_cacheKeys = array(
            'docs' => $this->modx->resource->getCacheKey().'/'.md5($cacheKey),
            'children' => $this->modx->resource->getCacheKey().'/'.md5($childrenCacheKey),
        );

        $this->_cacheOptions = array(
            'cache_key' => $this->modx->getOption('cache_resource_key',null, 'resource'),
            'cache_handler' => $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
            'cache_expires' => (int)$this->modx->getOption('cache_expires', null, 0),
        );
        return $this->_cacheKeys;
    }

    /**
     * Constructs the menu HTML by looping through the document array
     *
     * @return string The HTML for the menu
     */
    public function buildMenu() {
        $output = '';
        /* loop through all of the menu levels */
        foreach ($this->docs as $level => $subDocs) {
            /* loop through each document group (grouped by parent resource) */
            foreach ($subDocs as $parentId => $docs) {
                //if ($this->_config['startId'] != 0 && $this->_config['hideSubMenus']) continue;
                /* only process resource group, if starting at root, hidesubmenus is off, or is in current parenttree */
                if ((!$this->_config['hideSubMenus'] || $this->isHere($parentId) || $parentId == 0)) {

                    /* build the output for the group of resources */
                    $menuPart = $this->buildSubMenu($docs,$level);
                    /* if at the top of the menu start the output, otherwise replace the wrapper with the submenu */
                    if (($level == 1 && (!$this->_config['displayStart'] || $this->_config['id'] == 0)) || ($level == 0 && $this->_config['displayStart'])) {
                        $output = $menuPart;
                    } else {
                        $output = str_replace("[[+wf.wrapper.{$parentId}]]",$menuPart,$output);
                    }
                }
            }
        }
        return $output;
    }

    /**
     * Constructs a sub menu for the menu
     *
     * @param array $menuDocs Array of documents for the menu
     * @param int $level The heirarchy level of the sub menu to be rendered
     * @return string The submenu HTML
     */
    public function buildSubMenu($menuDocs,$level) {
        $subMenuOutput = '';
        $firstItem = 1;
        $counter = 1;
        $numSubItems = count($menuDocs);
        /* loop through each resource to render output */
        foreach ($menuDocs as $docId => $docInfo) {
            $docInfo['level'] = $level;
            $docInfo['first'] = $firstItem;
            $firstItem = 0;
            /* determine if last item in group */
            if ($counter == ($numSubItems) && $numSubItems > 1) {
                $docInfo['last'] = 1;
            } else {
                $docInfo['last'] = 0;
            }
            /* determine if resource has children */
            $docInfo['hasChildren'] = in_array($docInfo['id'],$this->hasChildren) ? 1 : 0;
            $numChildren = $docInfo['hasChildren'] ? count($this->docs[$level+1][$docInfo['id']]) : 0;
            /* render the row output */
            $subMenuOutput .= $this->renderRow($docInfo,$numChildren);
            /* update counter for last check */
            $counter++;
        }

        if ($level > 0) {
            /* determine which wrapper template to use */
            if ($this->_templates['innerTpl'] && $level > 1) {
                $useChunk = $this->_templates['innerTpl'];
                $usedTemplate = 'innerTpl';
            } else {
                $useChunk = $this->_templates['outerTpl'];
                $usedTemplate = 'outerTpl';
            }
            /* determine wrapper class */
            if ($level > 1) {
                $wrapperClass = 'innercls';
            } else {
                $wrapperClass = 'outercls';
            }
            /* get the class names for the wrapper */
            $classNames = $this->setItemClass($wrapperClass);
            $useClass = $classNames ? ' class="' . $classNames . '"' : '';
            $phArray = array($subMenuOutput,$useClass,$classNames);
            /* process the wrapper */
            $subMenuOutput = str_replace($this->placeHolders['wrapperLevel'],$phArray,$useChunk);
            /* debug */
            if ($this->_config['debug']) {
                $debugParent = $docInfo['parent'];
                $debugDocInfo = array();
                $debugDocInfo['template'] = $usedTemplate;
                foreach ($this->placeHolders['wrapperLevel'] as $n => $v) {
                    if ($v !== '[[+wf.wrapper]]') {
                        $debugDocInfo[$v] = $phArray[$n];
                    }
                }
                $this->addDebugInfo("wrapper","{$debugParent}","Wrapper for items with parent {$debugParent}.","These fields were used when processing the wrapper for the following resources: ",$debugDocInfo);
            }
        }
        return $subMenuOutput;
    }

    /**
     * Renders a row item for the menu
     *
     * @param array $resource An array containing the document information for the row
     * @param int $numChildren The number of children that the document contains
     * @return string The HTML for the row item
     */
    public function renderRow(&$resource,$numChildren) {
        $output = '';
        /* determine which template to use */
        if ($this->_config['displayStart'] && $resource['level'] == 0) {
            $usedTemplate = 'startItemTpl';
        } elseif ($resource['id'] == $this->_config['hereId'] && $resource['isfolder'] && $this->_templates['parentRowHereTpl'] && ($resource['level'] < $this->_config['level'] || $this->_config['level'] == 0) && $numChildren) {
            $usedTemplate = 'parentRowHereTpl';
        } elseif ($resource['id'] == $this->_config['hereId'] && $this->_templates['innerHereTpl'] && $resource['level'] > 1) {
            $usedTemplate = 'innerHereTpl';
        } elseif ($resource['id'] == $this->_config['hereId'] && $this->_templates['hereTpl']) {
            $usedTemplate = 'hereTpl';
        } elseif ($resource['isfolder'] && $this->_templates['activeParentRowTpl'] && ($resource['level'] < $this->_config['level'] || $this->_config['level'] == 0) && $this->isHere($resource['id'])) {
            $usedTemplate = 'activeParentRowTpl';
        } elseif ($resource['isfolder'] && ($resource['template']=="0" || is_numeric(strpos($resource['link_attributes'],'rel="category"'))) && $this->_templates['categoryFoldersTpl'] && ($resource['level'] < $this->_config['level'] || $this->_config['level'] == 0)) {
            $usedTemplate = 'categoryFoldersTpl';
        } elseif ($resource['isfolder'] && $this->_templates['parentRowTpl'] && ($resource['level'] < $this->_config['level'] || $this->_config['level'] == 0) && $numChildren) {
            $usedTemplate = 'parentRowTpl';
        } elseif ($resource['level'] > 1 && $this->_templates['innerRowTpl']) {
            $usedTemplate = 'innerRowTpl';
        } else {
            $usedTemplate = 'rowTpl';
        }
        /* get the template */
        $useChunk = $this->_templates[$usedTemplate];
        /* setup the new wrapper name and get the class names */
        $useSub = $resource['hasChildren'] ? "[[+wf.wrapper.{$resource['id']}]]" : "";
        $classNames = $this->setItemClass('rowcls',$resource['id'],$resource['first'],$resource['last'],$resource['level'],$resource['isfolder'],$resource['class_key']);
        $useClass = $classNames ? ' class="' . $classNames . '"' : '';
        /* setup the row id if a prefix is specified */
        if ($this->_config['rowIdPrefix']) {
            $useId = ' id="' . $this->_config['rowIdPrefix'] . $resource['id'] . '"';
        } else {
            $useId = '';
        }

        /* set placeholders for row */
        $placeholders = array();
        foreach ($resource as $k => $v) {
            $placeholders['wf.'.$k] = $v;
        }
        $placeholders['wf.wrapper'] = $useSub;
        $placeholders['wf.classes'] = $useClass;
        $placeholders['wf.classNames'] = $classNames;
        $placeholders['wf.classnames'] = $classNames;
        $placeholders['wf.id'] = $useId;
        $placeholders['wf.level'] = $resource['level'];
        $placeholders['wf.docid'] = $resource['id'];
        $placeholders['wf.subitemcount'] = $numChildren;
        $placeholders['wf.attributes'] = $resource['link_attributes'];
		
        if (!empty($this->tvList)) {
            $usePlaceholders = array_merge($placeholders,$this->placeHolders['tvs']);
            foreach ($this->tvList as $tvName) {
                $placeholders[$tvName]=$resource[$tvName];
            }
        } else {
            $usePlaceholders = $placeholders;
        }
        /* debug */
        if ($this->_config['debug']) {
            $debugDocInfo = array();
            $debugDocInfo['template'] = $usedTemplate;
            foreach ($usePlaceholders as $n => $v) {
                $debugDocInfo[$v] = $placeholders[$n];
            }
            $this->addDebugInfo("row","{$resource['parent']}:{$resource['id']}","Doc: #{$resource['id']}","The following fields were used when processing this document.",$debugDocInfo);
            $this->addDebugInfo("rowdata","{$resource['parent']}:{$resource['id']}","Doc: #{$resource['id']}","The following fields were retrieved from the database for this document.",$resource);
        }
        /* @var modChunk $chunk process content as chunk */
        $chunk = $this->modx->newObject('modChunk');
        $chunk->setCacheable(false);
        $output .= $chunk->process($placeholders, $useChunk);
		
        /* return the row */
        $separator = $this->modx->getOption('nl',$this->_config,"\n");
        return $output . $separator;
    }

    /**
     * Determine style class for current item being processed
     *
     * @param string $classType The type of class to be returned
     * @param int $docId The document ID of the item being processed
     * @param int $first Integer representing if the item is the first item (0 or 1)
     * @param int $last Integer representing if the item is the last item (0 or 1)
     * @param int $level The heirarchy level of the item being processed
     * @param int $isFolder Integer representing if the item is a container (0 or 1)
     * @param string $type Resource type of the item being processed
     * @return string The class string to use
     */
    public function setItemClass($classType, $docId = 0, $first = 0, $last = 0, $level = 0, $isFolder = 0, $type = 'modDocument') {
        $returnClass = '';
        $hasClass = 0;

        if ($classType === 'outercls' && !empty($this->_css['outer'])) {
            /* set outer class if specified */
            $returnClass .= $this->_css['outer'];
            $hasClass = 1;
        } elseif ($classType === 'innercls' && !empty($this->_css['inner'])) {
            /* set inner class if specified */
            $returnClass .= $this->_css['inner'];
            $hasClass = 1;
        } elseif ($classType === 'rowcls') {
            /* set row class if specified */
            if (!empty($this->_css['row'])) {
                $returnClass .= $this->_css['row'];
                $hasClass = 1;
            }
            /* set first class if specified */
            if ($first && !empty($this->_css['first'])) {
                $returnClass .= $hasClass ? ' ' . $this->_css['first'] : $this->_css['first'];
                $hasClass = 1;
            }
            /* set last class if specified */
            if ($last && !empty($this->_css['last'])) {
                $returnClass .= $hasClass ? ' ' . $this->_css['last'] : $this->_css['last'];
                $hasClass = 1;
            }
            /* set level class if specified */
            if (!empty($this->_css['level'])) {
                $returnClass .= $hasClass ? ' ' . $this->_css['level'] . $level : $this->_css['level'] . $level;
                $hasClass = 1;
            }
            /* set parentFolder class if specified */
            if ($isFolder && !empty($this->_css['parent']) && ($level < $this->_config['level'] || $this->_config['level'] == 0)) {
                $returnClass .= $hasClass ? ' ' . $this->_css['parent'] : $this->_css['parent'];
                $hasClass = 1;
            }
            /* set here class if specified */
            if (!empty($this->_css['here']) && $this->isHere($docId)) {
                $returnClass .= $hasClass ? ' ' . $this->_css['here'] : $this->_css['here'];
                $hasClass = 1;
            }
            /* set self class if specified */
            if (!empty($this->_css['self']) && $docId == $this->_config['hereId']) {
                $returnClass .= $hasClass ? ' ' . $this->_css['self'] : $this->_css['self'];
                $hasClass = 1;
            }
            /* set class for weblink */
            if (!empty($this->_css['weblink']) && $type == 'modWebLink') {
                $returnClass .= $hasClass ? ' ' . $this->_css['weblink'] : $this->_css['weblink'];
                $hasClass = 1;
            }
        }
        return $returnClass;
    }

    /**
     * Determine the "you are here" point in the menu
     *
     * @param $did Document ID to find
     * @return bool Returns true if the document ID was found
     */
    public function isHere($did) {
        return in_array($did,$this->parentTree);
    }

    /**
     * Add the specified CSS and Javascript chunks to the page
     *
     * @return void
     */
    public function regJsCss() {
        /* debug */
        if ($this->_config['debug']) {
            $jsCssDebug = array('js' => 'None Specified.', 'css' => 'None Specified.');
        }
        /* check and load the CSS */
        if (!empty($this->_config['cssTpl'])) {
            $cssChunk = $this->fetch($this->_config['cssTpl']);
            if ($cssChunk) {
                $this->modx->regClientCSS($cssChunk);
                if ($this->_config['debug']) {$jsCssDebug['css'] = "The CSS in {$this->_config['cssTpl']} was registered.";}
            } else {
                if ($this->_config['debug']) {$jsCssDebug['css'] = "The CSS in {$this->_config['cssTpl']} was not found.";}
            }
        }
        /* check and load the Javascript */
        if (!empty($this->_config['jsTpl'])) {
            $jsChunk = $this->fetch($this->_config['jsTpl']);
            if ($jsChunk) {
                $this->modx->regClientStartupScript($jsChunk);
                if ($this->_config['debug']) {$jsCssDebug['js'] = "The Javascript in {$this->_config['jsTpl']} was registered.";}
            } else {
                if ($this->_config['debug']) {$jsCssDebug['js'] = "The Javascript in {$this->_config['jsTpl']} was not found.";}
            }
        }
        /* debug */
        if ($this->_config['debug']) {$this->addDebugInfo('settings','JSCSS','JS/CSS Includes','Results of CSS & Javascript includes.',$jsCssDebug);}
    }

    /**
     * Smarter getChildIds that will iterate across Contexts if needed
     *
     * @param integer $startId The ID which to start at
     * @param integer $depth The depth in which to parse
     * @return array
     */
    public function getChildIds($startId = 0,$depth = 10) {
        $ids = array();
        if (!empty($this->_config['contexts'])) {
            $contexts = explode(',',$this->_config['contexts']);
            $contexts = array_unique($contexts);
            $currentContext = $this->modx->context->get('key');
            $activeContext = $currentContext;
            $switched = false;
            foreach ($contexts as $context) {
                if ($context != $currentContext) {
                    $this->modx->switchContext($context);
                    $switched = true;
                    $currentContext = $context;
                }
                /* use modx->getChildIds here, since we dont need to switch contexts within resource children */
                $contextIds = $this->modx->getChildIds($startId,$depth);
                if (!empty($contextIds)) {
                    $ids = array_merge($ids,$contextIds);
                }
            }
            $ids = array_unique($ids);
            if ($switched) { /* make sure to switch back to active context */
                $this->modx->switchContext($activeContext);
            }
        } else { /* much faster if not using contexts */
            $ids = $this->modx->getChildIds($startId,$depth);
        }
        return $ids;
    }

    /**
     * Get the required resources from the database to build the menu
     *
     * @return array The resource array of documents to be processed
     */
    public function getData() {
        $depth = !empty($this->_config['level']) ? $this->_config['level'] : 10;
        $ids = $this->getChildIds($this->_config['id'],$depth);
        $resourceArray = array();

        /* get all of the ids for processing */
        if ($this->_config['displayStart'] && $this->_config['id'] !== 0) {
            $ids[] = $this->_config['id'];
        }
        if (!empty($ids)) {
            $c = $this->modx->newQuery('modResource');
            $c->leftJoin('modResourceGroupResource','ResourceGroupResources');
            $c->query['distinct'] = 'DISTINCT';

            /* add the ignore hidden option to the where clause */
            if (!$this->_config['ignoreHidden']) {
                $c->where(array('hidemenu:=' => 0));
            }

            /* if set, limit results to specific resources */
            if (!empty($this->_config['includeDocs'])) {
                $c->where(array('modResource.id:IN' => explode(',',$this->_config['includeDocs'])));
            }

            /* add the exclude resources to the where clause */
            if (!empty($this->_config['contexts'])) {
                $c->where(array('modResource.context_key:IN' => explode(',',$this->_config['contexts'])));
                $c->sortby('context_key','DESC');
            }

            /* add the exclude resources to the where clause */
            if (!empty($this->_config['excludeDocs'])) {
                $c->where(array('modResource.id:NOT IN' => explode(',',$this->_config['excludeDocs'])));
            }
            
            /* add the limit to the query */
            if (!empty($this->_config['limit'])) {
                $offset = !empty($this->_config['offset']) ? $this->_config['offset'] : 0;
                $c->limit($this->_config['limit'], $offset);
            }

            /* JSON where ability */
            if (!empty($this->_config['where'])) {
                $where = $this->modx->fromJSON($this->_config['where']);
                if (!empty($where)) {
                    $c->where($where);
                }
            }
            if (!empty($this->_config['templates'])) {
                $c->where(array(
                    'template:IN' => explode(',',$this->_config['templates']),
                ));
            }

            /* determine sorting */
            if (strtolower($this->_config['sortBy']) == 'random') {
                $c->sortby('rand()', '');
            } else {
                $c->sortby($this->_config['sortBy'],$this->_config['sortOrder']);
            }

            $c->where(array('modResource.id:IN' =>  $ids));
            if ($this->modx->user->hasSessionContext('mgr') && $this->modx->hasPermission('view_unpublished') && $this->_config['previewUnpublished']) {} else {
                $c->where(array('modResource.published:=' => 1));
            }
            $c->where(array('modResource.deleted:=' => 0));

            /* not sure why this groupby is here in the first place. removing for now as it causes
             * issues with the sortby clauses */
            //$c->groupby($this->modx->getSelectColumns('modResource','modResource','',array('id')));

            $c->select($this->modx->getSelectColumns('modResource','modResource'));
            $c->select(array(
                'protected' => 'ResourceGroupResources.document_group',
            ));

            $result = $this->modx->getCollection('modResource', $c);


            $resourceArray = array();
            $level = 1;
            $prevParent = -1;
            /* setup start level for determining each items level */
            if ($this->_config['id'] == 0) {
                $startLevel = 0;
            } else {
                $activeContext = $this->modx->context->get('key');
                $contexts = !empty($this->_config['contexts']) ? explode(',',$this->_config['contexts']) : array();
                /* switching ctx, as this startId may not be in current Context */
                if (!empty($this->_config['startIdContext'])) {
                    $this->modx->switchContext($this->_config['startIdContext']);
                    $startLevel = count($this->modx->getParentIds($this->_config['id']));
                    $this->modx->switchContext($activeContext);

                /* attempt to auto-find startId context if &contexts param only has one context */
                } else if (!empty($contexts) && !empty($contexts[0]) && $contexts[0] != $activeContext) {
                    $this->modx->switchContext($contexts[0]);
                    $startLevel = count($this->modx->getParentIds($this->_config['id']));
                    $this->modx->switchContext($activeContext);

                } else {
                    $startLevel = count($this->modx->getParentIds($this->_config['id']));
                }
            }
            $resultIds = array();

            $activeContext = $this->modx->context->get('key');
            $currentContext = $activeContext;
            $switchedContext = false;
            /** @var modResource $doc */
            foreach ($result as $doc)  {
                $docContextKey = $doc->get('context_key');
                if (!empty($docContextKey) && $docContextKey != $currentContext) {
                    $this->modx->switchContext($docContextKey);
                    $switchedContext = true;
                    $currentContext = $doc->get('context_key');
                }
		        if ((!empty($this->_config['permissions'])) && (!$doc->checkPolicy($this->_config['permissions']))) continue;
                $tempDocInfo = $doc->toArray();
                $resultIds[] = $tempDocInfo['id'];
                $tempDocInfo['content'] = $tempDocInfo['class_key'] == 'modWebLink' ? $tempDocInfo['content'] : '';
                /* create the link */
                $linkScheme = $this->_config['fullLink'] ? 'full' : '';
                if (!empty($this->_config['scheme'])) $linkScheme = $this->_config['scheme'];

                if ($this->_config['useWeblinkUrl'] !== 'false' && $tempDocInfo['class_key'] == 'modWebLink') {
                    if (is_numeric($tempDocInfo['content'])) {
                        $tempDocInfo['link'] = $this->modx->makeUrl(intval($tempDocInfo['content']),'','',$linkScheme);
                    } else {
                        $tempDocInfo['link'] = $tempDocInfo['content'];
                    }
                } elseif ($tempDocInfo['id'] == $this->modx->getOption('site_start')) {
                    $tempDocInfo['link'] = $this->modx->getOption('site_url');
                } else {
                    $tempDocInfo['link'] = $this->modx->makeUrl($tempDocInfo['id'],'','',$linkScheme);
                }
                /* determine the level, if parent has changed */
                if ($prevParent !== $tempDocInfo['parent']) {
                    $level = count($this->modx->getParentIds($tempDocInfo['id'])) - $startLevel;
                }
                /* add parent to hasChildren array for later processing */
                if (($level > 1 || $this->_config['displayStart']) && !in_array($tempDocInfo['parent'],$this->hasChildren)) {
                    $this->hasChildren[] = $tempDocInfo['parent'];
                }
                /* set the level */
                $tempDocInfo['level'] = $level;
                $prevParent = $tempDocInfo['parent'];
                /* determine other output options */
                $useTextField = (empty($tempDocInfo[$this->_config['textOfLinks']])) ? 'pagetitle' : $this->_config['textOfLinks'];
                $tempDocInfo['linktext'] = $tempDocInfo[$useTextField];
                $tempDocInfo['title'] = $tempDocInfo[$this->_config['titleOfLinks']];
                $tempDocInfo['protected'] = !empty($tempDocInfo['protected']);
                if (!empty($this->tvList)) {
                    $tempResults[] = $tempDocInfo;
                } else {
                    $resourceArray[$tempDocInfo['level']][$tempDocInfo['parent']][] = $tempDocInfo;
                }
            }
            /* process the tvs */
            if (!empty($this->tvList) && !empty($resultIds)) {
                $tvValues = array();
                /* loop through all tvs and get their values for each resource */
                foreach ($this->tvList as $tvName) {
                    $tvValues = array_merge_recursive($this->appendTV($tvName,$resultIds),$tvValues);
                }
                /* loop through the document array and add the tvarpublic ues to each resource */
                foreach ($tempResults as $tempDocInfo) {
                    if (array_key_exists("#{$tempDocInfo['id']}",$tvValues)) {
                        foreach ($tvValues["#{$tempDocInfo['id']}"] as $tvName => $tvValue) {
                            $tempDocInfo[$tvName] = $tvValue;
                        }
                    }
                    $resourceArray[$tempDocInfo['level']][$tempDocInfo['parent']][] = $tempDocInfo;
                }
            }
            if (!empty($switchedContext)) {
                $this->modx->switchContext($activeContext);
            }
        }
        return $resourceArray;
    }

    /**
     * Append a TV to the resource array
     *
     * @param string $tvName Name of the Template Variable to append
     * @param array $docIds An array of document IDs to append the TV to
     * @return array A resource array with the TV information
     */
    public function appendTV($tvName,$docIds){
        $resourceArray = array();
        /** @var modTemplateVar $tv */
        if (empty($this->_cachedTVs[$tvName])) {
            $tv = $this->modx->getObject('modTemplateVar',array(
                'name' => $tvName,
            ));
        } else {
            $tv =& $this->_cachedTVs[$tvName];
        }
        if ($tv) {
            foreach ($docIds as $docId) {
                $resourceArray["#{$docId}"][$tvName] = $tv->renderOutput($docId);
            }
        }
        return $resourceArray;
    }

    /**
     * Check that templates are valid
     *
     * @return void
     */
    public function checkTemplates() {
        $nonWayfinderFields = array();

        foreach ($this->_templates as $n => $v) {
            $templateCheck = $this->fetch($v);
            if (empty($v) || !$templateCheck) {
                if ($n === 'outerTpl') {
                    $this->_templates[$n] = '<ul[[+wf.classes]]>[[+wf.wrapper]]</ul>';
                } elseif ($n === 'rowTpl') {
                    $this->_templates[$n] = '<li[[+wf.id]][[+wf.classes]]><a href="[[+wf.link]]" title="[[+wf.title]]" [[+wf.attributes]]>[[+wf.linktext]]</a>[[+wf.wrapper]]</li>';
                } elseif ($n === 'startItemTpl') {
                    $this->_templates[$n] = '<h2[[+wf.id]][[+wf.classes]]>[[+wf.linktext]]</h2>[[+wf.wrapper]]';
                } else {
                    $this->_templates[$n] = false;
                }
                if ($this->_config['debug']) { $this->addDebugInfo('template',$n,$n,"No template found, using default.",array($n => $this->_templates[$n])); }
            } else {
                $this->_templates[$n] = $templateCheck;
                $check = $this->findTemplateVars($templateCheck);
                if (is_array($check)) {
                    $nonWayfinderFields = array_merge($check, $nonWayfinderFields);
                }
                if ($this->_config['debug']) { $this->addDebugInfo('template',$n,$n,"Template Found.",array($n => $this->_templates[$n])); }
            }
        }

        if (!empty($nonWayfinderFields)) {
            $nonWayfinderFields = array_unique($nonWayfinderFields);

            foreach ($nonWayfinderFields as $field) {
                $this->placeHolders['tvs'][] = "{$field}";
                $this->tvList[] = $field;
            }
            if ($this->_config['debug']) { $this->addDebugInfo('tvars','tvs','Template Variables',"The following template variables were found in your templates.",$this->tvList); }
        }
    }

    /**
     * Fetch a template from the database or filesystem
     *
     * @param string $tpl Template to be fetched
     * @return string|bool Template HTML or false if no template was found
     */
    public function fetch($tpl) {
        /** @var modChunk $chunk */
        if ($chunk= $this->modx->getObject('modChunk', array ('name' => $tpl), true)) {
            $template = $chunk->getContent();
        } else if(substr($tpl, 0, 6) == "@FILE:") {
            $template = $this->get_file_contents(substr($tpl, 6));
        } else if(substr($tpl, 0, 6) == "@CODE:") {
            $template = substr($tpl, 6);
        } else if(substr($tpl, 0, 5) == "@FILE") {
            $template = $this->get_file_contents(trim(substr($tpl, 5)));
        } else if(substr($tpl, 0, 5) == "@CODE") {
            $template = trim(substr($tpl, 5));
        } else {
            $template = false;
        }
        return $template;
    }

    /**
     * Substitute function for file_get_contents()
     *
     * @param string $filename Name of file to be fetched
     * @return string The file contents
     */
    public function get_file_contents($filename) {
        if (!function_exists('file_get_contents')) {
            $fhandle = fopen($filename, "r");
            $fcontents = fread($fhandle, filesize($filename));
            fclose($fhandle);
        } else  {
            $fcontents = file_get_contents($filename);
        }
        return $fcontents;
    }

    public function findTemplateVars($tpl) {
        preg_match_all('~\[\[\+(.*?)\]\]~', $tpl, $matches);
        $TVs = array();
        foreach($matches[1] as $tv) {
            if (strpos($tv, "wf.") === false) {
            $match = explode(":", $tv);
            $TVs[strtolower($match[0])] = $match[0];
            }
        }
        if (count($TVs) >= 1) {
            return array_values($TVs);
        } else {
            return false;
        }
    }


    /**
     * Add debug information to the debug array
     *
     * @param string $group Group to attach the message to
     * @param string $groupkey Group key to attach the message to
     * @param string $header Title for the debug message
     * @param string $message The debug message
     * @param array $info An array of information to be added to the message as $key=>$value pairs
     * @return void
     */
    public function addDebugInfo($group,$groupkey,$header,$message,$info) {
        $infoString = '<table border="1" cellpadding="3px">';
        $numInfo = count($info);
        $count = 0;

        foreach ($info as $key => $value) {
            $key = $this->modxPrep($key);
            if ($value === true || $value === false) {
                $value = $value ? 'true' : 'false';
            } else {
                $value = $this->modxPrep($value);
            }
            if ($count == 2) { $infoString .= '</tr>'; $count = 0; }
            if ($count == 0) { $infoString .= '<tr>'; }
            $value = empty($value) ? '&nbsp;' : $value;
            $infoString .= "<td><strong>{$key}</strong></td><td>{$value}</td>";
            $count++;
        }
        $infoString .= '</tr></table>';

        $this->debugInfo[$group][$groupkey] = array(
            'header' => $this->modxPrep($header),
            'message' => $this->modxPrep($message),
            'info' => $infoString,
        );
    }

    /**
     * Render the debug array for display
     *
     * @return string HTML containing the rendered debug information
     */
    public function renderDebugOutput() {
        $output = '<table border="1" cellpadding="3px" width="100%">';
        foreach ($this->debugInfo as $group => $item) {
            switch ($group) {
                case 'template':
                    $output .= "<tr><th style=\"background:#C3D9FF;font-size:200%;\">Template Processing</th></tr>";
                    foreach ($item as $parentId => $info) {
                        $output .= "
                            <tr style=\"background:#336699;color:#fff;\"><th>{$info['header']} - <span style=\"font-weight:normal;\">{$info['message']}</span></th></tr>
                            <tr><td>{$info['info']}</td></tr>";
                    }
                    break;
                case 'wrapper':
                    $output .= "<tr><th style=\"background:#C3D9FF;font-size:200%;\">Document Processing</th></tr>";

                    foreach ($item as $parentId => $info) {
                        $output .= "<tr><table border=\"1\" cellpadding=\"3px\" style=\"margin-bottom: 10px;\" width=\"100%\">
                                    <tr style=\"background:#336699;color:#fff;\"><th>{$info['header']} - <span style=\"font-weight:normal;\">{$info['message']}</span></th></tr>
                                    <tr><td>{$info['info']}</td></tr>
                                    <tr style=\"background:#336699;color:#fff;\"><th>Documents included in this wrapper:</th></tr>";

                        foreach ($this->debugInfo['row'] as $key => $value) {
                            $keyParts = explode(':',$key);
                            if ($parentId == $keyParts[0]) {
                                $output .= "<tr style=\"background:#eee;\"><th>{$value['header']}</th></tr>
                                    <tr><td><div style=\"float:left;margin-right:1%;\">{$value['message']}<br />{$value['info']}</div><div style=\"float:left;\">{$this->debugInfo['rowdata'][$key]['message']}<br />{$this->debugInfo['rowdata'][$key]['info']}</div></td></tr>";
                            }
                        }

                        $output .= '</table></tr>';
                    }

                    break;
                case 'settings':
                    $output .= "<tr><th style=\"background:#C3D9FF;font-size:200%;\">Settings</th></tr>";
                    foreach ($item as $parentId => $info) {
                        $output .= "
                            <tr style=\"background:#336699;color:#fff;\"><th>{$info['header']} - <span style=\"font-weight:normal;\">{$info['message']}</span></th></tr>
                            <tr><td>{$info['info']}</td></tr>";
                    }
                    break;
                default:

                    break;
            }
        }
        $output .= '</table>';
        return $output;
    }

    /**
     * Preprocess values for rendering in the debug information
     *
     * @param string $value The value to be processed
     * @return string The processed value
     */
    public function modxPrep($value) {
        $value = (strpos($value,"<") !== false) ? htmlentities($value) : $value;
        $value = str_replace("[","&#091;",$value);
        $value = str_replace("]","&#093;",$value);
        $value = str_replace("{","&#123;",$value);
        $value = str_replace("}","&#125;",$value);
        return $value;
    }
}