<?php
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
 * AdminStructureController
 * Administrative API for site structure management
 * Requires authentication and admin privileges
 */
class AdminStructureController extends BaseController {

    private readonly StructureService $structureService;


    /**
     * Constructor
     */
    public function __construct(StructureService $structureService, Db $db, Request $request) {
        parent::__construct($db, $request);
        $this->structureService = $structureService;
    }


    /**
     * Get all pages for admin panel
     * GET /api/v1/admin/structure
     * 
     * Query parameters:
     * - status (string): Filter by status (draft, active, inactive)
     * - parent_id (int): Filter by parent ID
     * - search (string): Search in title and slug
     * - limit (int): Limit results (default: 50, max: 200)
     * - offset (int): Offset for pagination (default: 0)
     */
    public function index(): void {
        $this->log_request('admin_structure_index');

        try {
            $params = $this->get_query_params();
            
            $filters = [
                'status' => $params['status'] ?? null,
                'parent_id' => isset($params['parent_id']) ? (int)$params['parent_id'] : null,
                'search' => $params['search'] ?? null
            ];
            
            $limit = min(200, max(1, (int)($params['limit'] ?? 50)));
            $offset = max(0, (int)($params['offset'] ?? 0));

            $result = $this->structureService->get_admin_pages($filters, $limit, $offset);

            $this->json_response([
                'pages' => $result['pages'],
                'pagination' => $result['pagination'],
                'filters' => $filters
            ]);

        } catch (Exception $e) {
            error_log('Admin structure index error: ' . $e->getMessage());
            $this->error_response('Failed to load pages', 500);
        }
    }


    /**
     * Get page by ID for editing
     * GET /api/v1/admin/structure/{id}
     * 
     * @param int $id Page ID
     */
    public function show(int $id): void {
        $this->log_request('admin_structure_show', ['id' => $id]);

        if ($id <= 0) {
            $this->error_response('Invalid page ID', 400);
            return;
        }

        try {
            $page = $this->structureService->get_admin_page_by_id($id);

            if (!$page) {
                $this->not_found_response('Page not found');
                return;
            }

            $this->json_response($page);

        } catch (Exception $e) {
            error_log('Admin structure show error: ' . $e->getMessage());
            $this->error_response('Failed to load page', 500);
        }
    }


    /**
     * Create new page
     * POST /api/v1/admin/structure
     */
    public function create(): void {
        $this->log_request('admin_structure_create');

        try {
            $data = $this->get_input_data();
            
            $created_page = $this->structureService->create_page($data);

            if ($created_page) {
                $this->json_response($created_page, 201);
            } else {
                $this->error_response('Failed to create page', 500);
            }

        } catch (Exception $e) {
            error_log('Admin structure create error: ' . $e->getMessage());
            $this->error_response($e->getMessage(), 400);
        }
    }


    /**
     * Update existing page
     * PUT /api/v1/admin/structure/{id}
     * 
     * @param int $id Page ID
     */
    public function update(int $id): void {
        $this->log_request('admin_structure_update', ['id' => $id]);

        if ($id <= 0) {
            $this->error_response('Invalid page ID', 400);
            return;
        }

        try {
            $data = $this->get_input_data();
            
            $updated_page = $this->structureService->update_page($id, $data);

            if ($updated_page) {
                $this->json_response($updated_page);
            } else {
                $this->not_found_response('Page not found');
            }

        } catch (Exception $e) {
            error_log('Admin structure update error: ' . $e->getMessage());
            $this->error_response($e->getMessage(), 400);
        }
    }


    /**
     * Delete page
     * DELETE /api/v1/admin/structure/{id}
     * 
     * @param int $id Page ID
     */
    public function delete(int $id): void {
        $this->log_request('admin_structure_delete', ['id' => $id]);

        if ($id <= 0) {
            $this->error_response('Invalid page ID', 400);
            return;
        }

        // Addadditional protection against deleting the home page in the controller
        if ($id === 1) {
            $this->error_response('Cannot delete the home page (ID=1)', 403);
            return;
        }

        try {
            $success = $this->structureService->delete_page($id);

            if ($success) {
                $this->json_response([
                    'message' => 'Page deleted successfully',
                    'deleted_id' => $id
                ]);
            } else {
                $this->not_found_response('Page not found');
            }

        } catch (Exception $e) {
            error_log('Admin structure delete error: ' . $e->getMessage());
            $this->error_response($e->getMessage(), 400);
        }
    }


    /**
     * Change page status
     * PATCH /api/v1/admin/structure/{id}/status
     * 
     * @param int $id Page ID
     */
    public function change_status(int $id): void {
        $this->log_request('admin_structure_change_status', ['id' => $id]);

        if ($id <= 0) {
            $this->error_response('Invalid page ID', 400);
            return;
        }

        try {
            $data = $this->get_input_data();
            
            if (!isset($data['status']) || !in_array($data['status'], ['draft', 'active', 'inactive'])) {
                $this->error_response('Invalid status. Must be one of: draft, active, inactive', 400);
                return;
            }

            $success = $this->structureService->change_page_status($id, $data['status']);

            if ($success) {
                $this->json_response([
                    'message' => 'Status updated successfully',
                    'id' => $id,
                    'status' => $data['status']
                ]);
            } else {
                $this->not_found_response('Page not found');
            }

        } catch (Exception $e) {
            error_log('Admin structure change status error: ' . $e->getMessage());
            $this->error_response('Failed to change status', 500);
        }
    }


    /**
     * Reorder pages (change sort order)
     * PUT /api/v1/admin/structure/reorder
     */
    public function reorder(): void {
        $this->log_request('admin_structure_reorder');

        try {
            $data = $this->get_input_data();
            
            if (!isset($data['pages']) || !is_array($data['pages'])) {
                $this->error_response('Invalid data. Expected array of pages with id and sort fields', 400);
                return;
            }

            $success = $this->structureService->reorder_pages($data['pages']);

            if ($success) {
                $this->json_response([
                    'message' => 'Pages reordered successfully',
                    'updated_count' => count($data['pages'])
                ]);
            } else {
                $this->error_response('Failed to reorder pages', 500);
            }

        } catch (Exception $e) {
            error_log('Admin structure reorder error: ' . $e->getMessage());
            $this->error_response($e->getMessage(), 400);
        }
    }
}