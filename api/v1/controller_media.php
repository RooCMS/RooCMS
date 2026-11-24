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
 * Media Controller
 * Thin layer for handling HTTP requests/responses
 */
class MediaController extends BaseController {

	private FilesService $filesService;
	private Files $files;
	private AuthenticationService $authService;

	/** Viewer resolved once per request (optional authentication on public routes) */
	private ?array $viewer = null;
	private bool $viewer_resolved = false;



	/**
	 * Constructor
	 *
	 * @param Db $db Database instance
	 * @param FilesService $filesService Files service
	 * @param Files $files Files class for constants access
	 * @param AuthenticationService $authService Used for optional token check on public routes
	 */
	public function __construct(Db $db, Request $request, FilesService $filesService, Files $files, AuthenticationService $authService) {
		parent::__construct($db, $request);
		$this->filesService = $filesService;
		$this->files = $files;
		$this->authService = $authService;
	}


	/**
	 * Resolve current viewer on public routes (token is optional)
	 * Invalid/expired token is treated as guest
	 */
	private function get_viewer(): ?array {
		if($this->viewer_resolved) {
			return $this->viewer;
		}
		$this->viewer_resolved = true;

		$user = $this->get_authenticated_user();
		if(empty($user)) {
			try {
				$token = get_bearer_token();
				$user = $token ? $this->authService->verify_token($token) : null;
			} catch(Throwable $e) {
				$user = null;
			}
		}

		$this->viewer = !empty($user) ? $user : null;
		return $this->viewer;
	}


	/**
	 * Moderator/admin/superuser may see everything
	 */
	private function is_privileged_viewer(): bool {
		$viewer = $this->get_viewer();
		return $viewer !== null && in_array($viewer['role'] ?? null, [Role::MODERATOR, Role::ADMIN, Role::SUPERUSER], true);
	}


	/**
	 * Can viewer see full record (privileged or owner)
	 */
	private function can_view_full(array $media): bool {
		if($this->is_privileged_viewer()) {
			return true;
		}
		$viewer = $this->get_viewer();
		return $viewer !== null && isset($media['user_id']) && (int)$media['user_id'] === (int)$viewer['id'];
	}


	/**
	 * Remove fields that must not be exposed to non-privileged viewers
	 */
	private function strip_private_fields(array $media): array {
		unset($media['user_id'], $media['metadata']);
		return $media;
	}


	/**
	 * Get list of media files with pagination and filtering
	 * GET /api/v1/media
	 */
	public function index(): void {
		$this->log_request('media_list');
		
		try {
			// Parse and validate query parameters
			$pagination = $this->get_pagination_params();
			$page = $pagination['page'];
			$limit = $pagination['limit'];
			$type = $this->request->get['type'] ?? null;
			$status = $this->request->get['status'] ?? null;
			$user_id = isset($this->request->get['user_id']) ? (int)$this->request->get['user_id'] : null;
			$search = $this->request->get['search'] ?? null;
			
			// Basic validation using Media class constants
			if($type && !in_array($type, $this->files::TYPES, true)) {
				$this->error_response('Invalid media type', 400);
				return;
			}
			
			if($status && !in_array($status, $this->files::STATUS_TYPES, true)) {
				$this->error_response('Invalid status', 400);
				return;
			}
			
			// Build filters
			$filters = [];
			if($type) $filters['media_type'] = $type;
			if($status) $filters['status'] = $status;
			if($user_id) $filters['user_id'] = $user_id;
			if($search) $filters['search'] = $search;
			
			// Non-privileged viewers: only ready media, no filtering by uploader
			$privileged = $this->is_privileged_viewer();
			if(!$privileged) {
				$filters['status'] = 'ready';
				unset($filters['user_id']);
			}

			// Delegate to service (same filters for list and count)
			$media_files = $this->filesService->get_media_list($filters, $page, $limit);
			$total = $this->filesService->get_media_count($filters);

			if(!$privileged) {
				$media_files = array_map(fn(array $item): array => $this->strip_private_fields($item), $media_files);
			}
			
			// Prepare response
			$response = [
				'data' => $media_files,
				'pagination' => [
					'current_page' => $page,
					'per_page' => $limit,
					'total' => $total,
					'total_pages' => (int)ceil($total / $limit),
					'has_next' => $page * $limit < $total,
					'has_prev' => $page > 1
				],
				'filters' => $filters
			];
			
			$this->json_response($response);
			
		} catch(Exception $e) {
			error_log('media list: ' . $e->getMessage());
			$this->error_response('Failed to get media list', 500);
		}
	}


	/**
	 * Get specific media file info
	 * GET /api/v1/media/{id}
	 */
	public function show(int $id): void {
		$this->log_request('media_show', ['id' => $id]);
		
		try {
			// Delegate to service
			$media = $this->filesService->get_file_formatted($id);

			if(!$media) {
				$this->error_response('Media not found', 404);
				return;
			}

			if(!$this->can_view_full($media)) {
				if(($media['status'] ?? null) !== 'ready') {
					$this->error_response('Media not found', 404);
					return;
				}
				$media = $this->strip_private_fields($media);
			}

			$this->json_response($media);
			
		} catch(Exception $e) {
			error_log('media show: ' . $e->getMessage());
			$this->error_response('Failed to get media info', 500);
		}
	}


	/**
	 * Download or get file content
	 * GET /api/v1/media/{id}/file
	 */
	public function download(int $id): void {
		$this->log_request('media_download', ['id' => $id]);
		
		try {
			// Get media info from service
			$media = $this->filesService->get_file($id);
			
			if(!$media || (!$this->can_view_full($media) && ($media['status'] ?? null) !== 'ready')) {
				$this->error_response('Media not found', 404);
				return;
			}

			// Build file path
			$file_path = _UPLOAD . $media['file_path'] . '/' . $media['filename'];
			
			// Check variant request
			$variant = $this->request->get['variant'] ?? null;
			if($variant && in_array($variant, ['thumbnail', 'large', 'original'], true)) {
				$variant_info = $this->filesService->get_variant_file($id, $variant);
				if($variant_info) {
					$file_path = _UPLOAD . $variant_info['file_path'];
					$media['filename'] = basename($variant_info['file_path']);
					$media['mime_type'] = $variant_info['mime_type'] ?? $media['mime_type'];
				}
			}
			
			// Validate file exists
			if(!file_exists($file_path) || !is_readable($file_path)) {
				$this->error_response('File not accessible', 404);
				return;
			}
			
			// Send file with proper headers
			set_header('Content-Type: ' . $media['mime_type']);
			set_header('Content-Length: ' . filesize($file_path));
			$raw_name = (string)($media['original_name'] ?? '');
			$utf8_name = preg_replace('/[\x00-\x1F\x7F]/', '', $raw_name) ?? '';
			$ascii_name = preg_replace('/[^\x20-\x7E]|["\\\\\/]/', '_', $utf8_name) ?? '';
			if(trim($ascii_name, " _.") === '') $ascii_name = 'download';
			set_header('Content-Disposition: attachment; filename="' . $ascii_name . '"; filename*=UTF-8\'\'' . rawurlencode($utf8_name));
			set_header('Cache-Control: private, max-age=3600');
			set_header('ETag: "' . md5_file($file_path) . '"');
			
			if(readfile($file_path) === false) {
				$this->error_response('Failed to read file', 500);
			}
			
		} catch(Exception $e) {
			error_log('media download: ' . $e->getMessage());
			$this->error_response('Failed to download file', 500);
		}
	}


	/**
	 * Upload new media file
	 * POST /api/v1/media/upload
	 */
	public function upload(): void {
		$this->log_request('media_upload');
		
		try {
			// Basic validation
			if(!isset($this->request->files['file'])) {
				$this->error_response('No file provided', 400);
				return;
			}
			
			$file = $this->request->files['file'];
			
			if($file['error'] !== UPLOAD_ERR_OK) {
				$this->error_response('File upload error: ' . $this->get_upload_error_message($file['error']), 400);
				return;
			}
			
			// Parse parameters
			$user_id = $this->require_authentication()['id'];
			$description = $this->request->post['description'] ?? null;
			$tags = $this->request->post['tags'] ?? null;
			$entity_type = $this->request->post['entity_type'] ?? null;
			$entity_id = isset($this->request->post['entity_id']) ? (int)$this->request->post['entity_id'] : null;
			$relationship_type = $this->request->post['relationship_type'] ?? 'attachment';

			// Prepare options
			$options = [];
			if($description) $options['description'] = $description;
			if($tags) $options['tags'] = $tags;
			
			// Delegate upload to service
			$media_id = $this->filesService->upload_file($file, $user_id, $options);
			
			// Attach to entity if provided
			if($entity_type && $entity_id) {
				$this->filesService->attach_to_entity($media_id, $entity_type, $entity_id, $relationship_type);
			}
			
			// Get formatted media info
			$media = $this->filesService->get_file_formatted($media_id);
			
			$this->json_response([
				'message' => 'File uploaded successfully',
				'media' => $media
			], 201);
			
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode() ?: 400);
		} catch(Exception $e) {
			error_log('media upload: ' . $e->getMessage());
			$this->error_response('Failed to upload file', 500);
		}
	}


	/**
	 * Update media metadata
	 * PUT /api/v1/media/{id}
	 */
	public function update(int $id): void {
		$this->log_request('media_update', ['id' => $id]);
		
		try {
			// Require authentication
			$current_user = $this->require_authentication();
			if(empty($current_user)) {
				return; // Error response already sent
			}
			
			// Parse JSON input
			$input = $this->get_input_data();
			
			// Delegate to service with current user info
			$updated_media = $this->filesService->update_media_metadata($id, $input, $current_user);
			
			$this->json_response([
				'message' => 'Media updated successfully',
				'media' => $updated_media
			]);
			
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode() ?: 400);
		} catch(Exception $e) {
			error_log('media update: ' . $e->getMessage());
			$this->error_response('Failed to update media', 500);
		}
	}


	/**
	 * Delete media file
	 * DELETE /api/v1/media/{id}
	 */
	public function delete(int $id): void {
		$this->log_request('media_delete', ['id' => $id]);
		
		try {
			// Require authentication
			$current_user = $this->require_authentication();
			if(empty($current_user)) {
				return; // Error response already sent
			}
			
			// Delegate to service with current user info
			$success = $this->filesService->delete_file($id, $current_user);
			
			if(!$success) {
				$this->error_response('Failed to delete media', 500);
				return;
			}
			
			$this->json_response([
				'message' => 'Media deleted successfully',
				'id' => $id
			]);
			
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode() ?: 400);
		} catch(Exception $e) {
			error_log('media delete: ' . $e->getMessage());
			$this->error_response('Failed to delete media', 500);
		}
	}
}