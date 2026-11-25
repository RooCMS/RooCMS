<?php declare(strict_types=1);

require_once _MODULES.'/class_structure.php';
require_once _SERVICES.'/structureCommon.php';
require_once _SERVICES.'/structure.php';

class StructurePathTest extends TestCase {
    private function tree(array $pages): Structure {
        $structure = (new ReflectionClass(Structure::class))->newInstanceWithoutConstructor();
        $pages = array_map(static fn(array $page): array => $page + [
            'slug' => $page['id'] === 1 ? 'index' : 'p'.$page['id'],
            'title' => 'Page '.$page['id'], 'status' => 'active', 'parent' => 0
        ], $pages);
        $structure->sitetree = (new ReflectionMethod(Structure::class, 'construct_tree'))->invoke(
            $structure, array_column($pages, null, 'id')
        );
        (new ReflectionMethod(Structure::class, 'update_tree_parent'))->invoke($structure);
        return $structure;
    }

    public function testRootSelfReferenceIsIncludedOnlyOnce(): void {
        $pages = [
            ['id' => 1, 'parent_id' => 1],
            ['id' => 2, 'parent_id' => 1],
            ['id' => 3, 'parent_id' => 2],
        ];
        $tree = $this->tree($pages)->sitetree;
        $this->assertSame([1, 2, 3], array_keys($tree));
        $this->assertSame([0, 1, 2], array_column($tree, 'level'));
        $this->assertSame('/', $tree[1]['structure_path']);
        $this->assertSame('/p2/p3', $tree[3]['structure_path']);
    }

    public function testAllAncestorsAreIncludedBeyondTenLevels(): void {
        $pages = [['id' => 1, 'parent_id' => 0]];
        for ($id = 2; $id <= 15; $id++) {
            $pages[] = ['id' => $id, 'parent_id' => $id - 1];
        }
        $tree = $this->tree($pages)->sitetree;
        $this->assertSame(range(1, 15), array_keys($tree));
        $this->assertSame(14, $tree[15]['level']);
        $this->assertSame('/'.implode('/', array_map(static fn(int $id): string => 'p'.$id, range(2, 15))), $tree[15]['structure_path']);
    }

    public function testFilteringAndPaginationUseConstructedTreeWithoutDatabase(): void {
        $structure = $this->tree([
            ['id' => 1, 'parent_id' => 1],
            ['id' => 2, 'parent_id' => 3],
            ['id' => 3, 'parent_id' => 1],
            ['id' => 4, 'parent_id' => 1],
        ]);
        $this->assertSame([1, 3, 2, 4], array_column($structure->get_admin_pages()['pages'], 'id'));
        $page = $structure->get_admin_pages(limit: 1, offset: 2)['pages'][0];
        $this->assertSame(2, $page['id']);
        $this->assertSame('/p3/p2', $page['structure_path']);
        $filtered = $structure->get_admin_pages(['search' => 'p2', 'parent_id' => 3]);
        $this->assertSame(1, $filtered['total']);
        $this->assertSame($page, $filtered['pages'][0]);
        $this->assertSame(0, $structure->get_admin_pages(['status' => 'draft'])['total']);
    }

    public function testBreadcrumbsUseParentChainWithoutDatabase(): void {
        $pages = [['id' => 1, 'parent_id' => 1]];
        for ($id = 2; $id <= 15; $id++) {
            $pages[] = ['id' => $id, 'parent_id' => $id - 1];
        }
        $structure = $this->tree($pages);
        $structure->page_id = 15;
        $service = (new ReflectionClass(StructureService::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(StructureService::class, 'structure'))->setValue($service, $structure);
        $breadcrumbs = $service->get_breadcrumbs();
        $this->assertSame(['index', ...array_map(static fn(int $id): string => 'p'.$id, range(2, 15))], array_column($breadcrumbs, 'slug'));
        $this->assertSame('/', $breadcrumbs[0]['url']);
        $this->assertSame('/p15', $breadcrumbs[14]['url']);
        $this->assertTrue($breadcrumbs[14]['is_current']);
        $this->assertSame(1, count(array_filter($breadcrumbs, static fn(array $crumb): bool => $crumb['is_current'])));
    }
}
