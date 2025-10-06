<?php declare(strict_types=1);
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see https://www.gnu.org/licenses/
 */

//#########################################################
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################


/**
 * StructureService
 * Service layer for working with site structure
 * Contains business logic, validation, and error handling
 */
class StructureService {

    private Structure $structure;
    private SiteSettings $settings;


    /**
     * Constructor
     * 
     * @param Structure $structure Модель структуры
     * @param SiteSettings $settings Настройки сайта
     */
    public function __construct(Structure $structure, SiteSettings $settings) {
        $this->structure = $structure;
        $this->settings = $settings;
    }


    /**
     * Get site tree with filters
     * 
     * @param int $parent_id Parent ID (default 0 - корень)
     * @param int $max_level Максимальная глубина вложенности (0 = без ограничений)
     * @param bool $include_children Включать дочерние элементы
     * @param bool $only_published Только опубликованные страницы
     * @param bool $only_navigation Только страницы для навигации
     * @return array|null Дерево структуры или null при ошибке
     */
    public function get_site_tree(int $parent_id = 0, int $max_level = 0, bool $include_children = true, bool $only_published = false, bool $only_navigation = false): ?array {
        try {
            $tree = $this->structure->load_tree($parent_id, $max_level, $include_children);
            
            if ($tree === false) {
                return null;
            }

            // Применяем фильтры
            if ($only_published || $only_navigation) {
                $tree = $this->filter_tree($tree, $only_published, $only_navigation);
            }

            return $tree;
        } catch (Exception $e) {
            error_log('Error loading site tree: ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Get page by ID with validation
     * 
     * @param int $page_id ID страницы
     * @return array|null Данные страницы или null если не найдена
     */
    public function get_page_by_id(int $page_id): ?array {
        if ($page_id <= 0) {
            return null;
        }

        try {
            $page = $this->structure->get_page_by_id($page_id);
            
            if ($page) {
                return $this->format_page_data($page);
            }
            
            return null;
        } catch (Exception $e) {
            error_log('Error getting page by ID ' . $page_id . ': ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Get page by slug with validation
     * 
     * @param string $slug Slug страницы
     * @return array|null Данные страницы или null если не найдена
     */
    public function get_page_by_slug(string $slug): ?array {
        $slug = trim($slug);
        
        if (empty($slug) || !$this->validate_slug($slug)) {
            return null;
        }

        try {
            $page = $this->structure->get_page_by_slug($slug);
            
            if ($page) {
                return $this->format_page_data($page);
            }
            
            return null;
        } catch (Exception $e) {
            error_log('Error getting page by slug "' . $slug . '": ' . $e->getMessage());
            return null;
        }
    }


    /**
     * Load page data and set as current
     * 
     * @param string|int|null $identifier ID страницы или slug
     * @return bool Успешность операции
     */
    public function load_current_page(string|int|null $identifier = null): bool {
        try {
            $this->structure->load_page_data($identifier);
            return true;
        } catch (Exception $e) {
            error_log('Error loading current page: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Get data of current page
     * 
     * @return array Данные текущей страницы
     */
    public function get_current_page(): array {
        try {
            $page = $this->structure->get_current_page();
            return $this->format_page_data($page);
        } catch (Exception $e) {
            error_log('Error getting current page: ' . $e->getMessage());
            return $this->get_default_page_data();
        }
    }


    /**
     * Get navigation menu
     * 
     * @param int $parent_id Родительский ID (0 для главного меню)
     * @param int $max_level Максимальная глубина меню
     * @return array Элементы навигации
     */
    public function get_navigation_menu(int $parent_id = 0, int $max_level = 3): array {
        try {
            $tree = $this->get_site_tree($parent_id, $max_level, true, true, true);
            
            if (!$tree) {
                return [];
            }

            return $this->build_navigation_array($tree);
        } catch (Exception $e) {
            error_log('Error building navigation menu: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * Get breadcrumbs for current page
     * 
     * @return array Массив хлебных крошек
     */
    public function get_breadcrumbs(): array {
        try {
            $current_page = $this->structure->get_current_page();
            $breadcrumbs = [];
            
            // Добавляем главную страницу
            if ($current_page['id'] !== 1) {
                $home_page = $this->get_page_by_id(1);
                if ($home_page) {
                    $breadcrumbs[] = [
                        'title' => $home_page['title'],
                        'slug' => $home_page['slug'],
                        'url' => '/',
                        'is_current' => false
                    ];
                }
            }

            // Строим путь до текущей страницы
            $path = $this->build_page_path($current_page['id']);
            
            foreach ($path as $page) {
                if ($page['id'] === 1) continue; // Главную уже добавили
                
                $breadcrumbs[] = [
                    'title' => $page['title'],
                    'slug' => $page['slug'],
                    'url' => '/' . $page['slug'],
                    'is_current' => $page['id'] === $current_page['id']
                ];
            }

            return $breadcrumbs;
        } catch (Exception $e) {
            error_log('Error building breadcrumbs: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * Get SEO metadata for current page
     * 
     * @return array SEO данные
     */
    public function get_seo_meta(): array {
        try {
            $current = $this->structure->get_current_page();
            
            return [
                'title' => $current['meta_title'] ?: $current['title'],
                'description' => $current['meta_description'] ?: $this->settings->get('site_description', ''),
                'keywords' => $current['meta_keywords'],
                'noindex' => (bool)$current['noindex'],
                'canonical' => $this->build_canonical_url($current['slug']),
                'og_title' => $current['title'],
                'og_description' => $current['meta_description'] ?: $this->settings->get('site_description', ''),
                'og_type' => 'website'
            ];
        } catch (Exception $e) {
            error_log('Error getting SEO meta: ' . $e->getMessage());
            return $this->get_default_seo_meta();
        }
    }


    /**
     * Validate slug
     * 
     * @param string $slug Slug для проверки
     * @return bool Результат валидации
     */
    private function validate_slug(string $slug): bool {
        // Проверяем длину
        if (strlen($slug) > 255) {
            return false;
        }

        // Проверяем допустимые символы (латиница, цифры, дефис, подчеркивание)
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $slug)) {
            return false;
        }

        // Slug не должен начинаться или заканчиваться дефисом
        if (str_starts_with($slug, '-') || str_ends_with($slug, '-')) {
            return false;
        }

        return true;
    }


    /**
     * Filter tree of pages
     * 
     * @param array $tree Дерево страниц
     * @param bool $only_published Только опубликованные
     * @param bool $only_navigation Только для навигации
     * @return array Отфильтрованное дерево
     */
    private function filter_tree(array $tree, bool $only_published = false, bool $only_navigation = false): array {
        $filtered = [];

        foreach ($tree as $page) {
            // Фильтр по навигации
            if ($only_navigation && !$page['nav']) {
                continue;
            }

            // Фильтр по доступности (для будущей реализации published статуса)
            if ($only_published && !$page['access']) {
                continue;
            }

            $filtered[$page['id']] = $page;
        }

        return $filtered;
    }


    /**
     * Formatting page data
     * 
     * @param array $page Сырые данные страницы
     * @return array Отформатированные данные
     */
    private function format_page_data(array $page): array {
        return [
            'id' => (int)$page['id'],
            'parent_id' => (int)$page['parent_id'],
            'slug' => (string)$page['slug'],
            'title' => (string)$page['title'],
            'meta_title' => (string)($page['meta_title'] ?? $page['title']),
            'meta_description' => (string)($page['meta_description'] ?? ''),
            'meta_keywords' => (string)($page['meta_keywords'] ?? ''),
            'noindex' => (bool)($page['noindex'] ?? false),
            'page_type' => (string)($page['page_type'] ?? 'page'),
            'nav' => (bool)($page['nav'] ?? false),
            'sort' => (int)($page['sort'] ?? 0),
            'childs' => (int)($page['childs'] ?? 0),
            'level' => (int)($page['level'] ?? 0),
            'access' => (bool)($page['access'] ?? true),
            'created_at' => (int)($page['created_at'] ?? 0),
            'updated_at' => (int)($page['updated_at'] ?? 0),
            'url' => $this->build_page_url($page['slug'])
        ];
    }


    /**
     * Building navigation array
     * 
     * @param array $tree Дерево страниц
     * @return array array navigation
     */
    private function build_navigation_array(array $tree): array {
        $navigation = [];

        foreach ($tree as $page) {
            if (!$page['nav']) continue;

            $nav_item = [
                'id' => $page['id'],
                'title' => $page['title'],
                'slug' => $page['slug'],
                'url' => $this->build_page_url($page['slug']),
                'level' => $page['level'],
                'has_children' => $page['childs'] > 0,
                'sort' => $page['sort']
            ];

            $navigation[] = $nav_item;
        }

        // Сортировка по полю sort
        usort($navigation, fn($a, $b) => $a['sort'] <=> $b['sort']);

        return $navigation;
    }


    /**
     * Build path to page (breadcrumbs)
     * 
     * @param int $page_id ID страницы
     * @return array Путь до страницы
     */
    private function build_page_path(int $page_id): array {
        $path = [];
        $current_id = $page_id;

        // Предотвращаем бесконечный цикл
        $max_depth = 10;
        $depth = 0;

        while ($current_id > 0 && $depth < $max_depth) {
            $page = $this->structure->get_page_by_id($current_id);
            
            if (!$page) {
                break;
            }

            array_unshift($path, $page);
            $current_id = (int)$page['parent_id'];
            $depth++;
        }

        return $path;
    }


    /**
     * Build page URL
     * 
     * @param string $slug Slug страницы
     * @return string URL страницы
     */
    private function build_page_url(string $slug): string {
        if ($slug === 'index') {
            return '/';
        }
        
        return '/' . ltrim($slug, '/');
    }


    /**
     * Build canonical URL
     * 
     * @param string $slug Slug page
     * @return string Canonical URL
     */
    private function build_canonical_url(string $slug): string {
        $domain = $this->settings->get_by_key('site_domain');
        
        return 'https://' . $domain . $this->build_page_url($slug);
    }


    /**
     * Get default page data
     * 
     * @return array Default page data
     */
    private function get_default_page_data(): array {
        return [
            'id' => 1,
            'parent_id' => 0,
            'slug' => 'index',
            'title' => $this->settings->get_by_key('site_name'),
            'meta_title' => $this->settings->get_by_key('site_name'),
            'meta_description' => $this->settings->get_by_key('site_description'),
            'meta_keywords' => '',
            'noindex' => false,
            'page_type' => 'page',
            'nav' => true,
            'sort' => 1,
            'childs' => 0,
            'level' => 0,
            'access' => true,
            'created_at' => time(),
            'updated_at' => time(),
            'url' => '/'
        ];
    }


    /**
     * Get default SEO metadata
     * 
     * @return array Default SEO metadata
     */
    private function get_default_seo_meta(): array {
        return [
            'title' => $this->settings->get_by_key('site_name'),
            'description' => $this->settings->get_by_key('site_description'),
            'keywords' => '',
            'noindex' => false,
            'canonical' => $this->build_canonical_url('index'),
            'og_title' => $this->settings->get_by_key('site_name'),
            'og_description' => $this->settings->get_by_key('site_description'),
            'og_type' => 'website'
        ];
    }

}