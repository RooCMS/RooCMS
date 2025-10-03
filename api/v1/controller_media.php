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

	private MediaService $media_service;
	private Media $media;

	/**
	 * Constructor
	 * 
	 * @param Db $db Database instance
	 * @param MediaService $media_service Media service
	 * @param Media $media Media class for constants access
	 */
	public function __construct(Db $db, Request $request, MediaService $media_service, Media $media) {
		parent::__construct($db, $request);
		$this->media_service = $media_service;
		$this->media = $media;
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
			$type = $request->get['type'] ?? null;
			$status = $request->get['status'] ?? null;
			$user_id = isset($request->get['user_id']) ? (int)$request->get['user_id'] : null;
			$search = $request->get['search'] ?? null;
			
			// Basic validation using Media class constants
			if($type && !in_array($type, $this->media::TYPES, true)) {
				$this->error_response('Invalid media type', 400);
				return;
			}
			
			if($status && !in_array($status, $this->media::STATUSES, true)) {
				$this->error_response('Invalid status', 400);
				return;
			}
			
			// Build filters
			$filters = [];
			if($type) $filters['media_type'] = $type;
			if($status) $filters['status'] = $status;
			if($user_id) $filters['user_id'] = $user_id;
			if($search) $filters['search'] = $search;
			
			// Delegate to service
			$media_files = $this->media_service->get_media_list($filters, $page, $limit);
			$total = $this->media_service->get_media_count($filters);
			
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
			$this->error_response('Failed to get media list: ' . $e->getMessage(), 500);
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
			$media = $this->media_service->get_file_formatted($id);
			
			if(!$media) {
				$this->error_response('Media not found', 404);
				return;
			}
			
			$this->json_response($media);
			
		} catch(Exception $e) {
			$this->error_response('Failed to get media info: ' . $e->getMessage(), 500);
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
			$media = $this->media_service->get_file($id);
			
			if(!$media) {
				$this->error_response('Media not found', 404);
				return;
			}
			
			// Build file path
			$file_path = _UPLOAD . $media['file_path'] . '/' . $media['filename'];
			
			// Check variant request
			$variant = $request->get['variant'] ?? null;
			if($variant && in_array($variant, ['thumbnail', 'large', 'original'], true)) {
				$variant_info = $this->media_service->get_variant_file($id, $variant);
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
			header('Content-Type: ' . $media['mime_type']);
			header('Content-Length: ' . filesize($file_path));
			header('Content-Disposition: attachment; filename="' . $media['original_name'] . '"');
			header('Cache-Control: private, max-age=3600');
			header('ETag: "' . md5_file($file_path) . '"');
			
			if(readfile($file_path) === false) {
				$this->error_response('Failed to read file', 500);
			}
			
		} catch(Exception $e) {
			$this->error_response('Failed to download file: ' . $e->getMessage(), 500);
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
			if(!isset($request->files['file'])) {
				$this->error_response('No file provided', 400);
				return;
			}
			
			$file = $request->files['file'];
			
			if($file['error'] !== UPLOAD_ERR_OK) {
				$this->error_response('File upload error: ' . $this->get_upload_error_message($file['error']), 400);
				return;
			}
			
			// Parse parameters
			$user_id = $this->require_authentication()['id'];
			$description = $request->post['description'] ?? null;
			$tags = $request->post['tags'] ?? null;
			$entity_type = $request->post['entity_type'] ?? null;
			$entity_id = isset($request->post['entity_id']) ? (int)$request->post['entity_id'] : null;
			$relationship_type = $request->post['relationship_type'] ?? 'attachment';
			
			// Prepare options
			$options = [];
			if($description) $options['description'] = $description;
			if($tags) $options['tags'] = $tags;
			
			// Delegate upload to service
			$media_id = $this->media_service->upload_file($file, $user_id, $options);
			
			// Attach to entity if provided
			if($entity_type && $entity_id) {
				$this->media_service->attach_to_entity($media_id, $entity_type, $entity_id, $relationship_type);
			}
			
			// Get formatted media info
			$media = $this->media_service->get_file_formatted($media_id);
			
			$this->json_response([
				'message' => 'File uploaded successfully',
				'media' => $media
			], 201);
			
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode());
		} catch(Exception $e) {
			$this->error_response('Failed to upload file: ' . $e->getMessage(), 500);
		}
	}


	/**
	 * Update media metadata
	 * PUT /api/v1/media/{id}
	 */
	public function update(int $id): void {
		$this->log_request('media_update', ['id' => $id]);
		
		try {
			// Parse JSON input
			$input = $this->get_json_input();
			
			$updated_media = $this->media_service->update_media_metadata($id, $input);
			
			$this->json_response([
				'message' => 'Media updated successfully',
				'media' => $updated_media
			]);
			
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode());
		} catch(Exception $e) {
			$this->error_response('Failed to update media: ' . $e->getMessage(), 500);
		}
	}


	/**
	 * Delete media file
	 * DELETE /api/v1/media/{id}
	 */
	public function delete(int $id): void {
		$this->log_request('media_delete', ['id' => $id]);
		
		try {
			// Delegate to service
			$success = $this->media_service->delete_file($id);
			
			if(!$success) {
				$this->error_response('Failed to delete media', 500);
				return;
			}
			
			$this->json_response([
				'message' => 'Media deleted successfully',
				'id' => $id
			]);
			
		} catch(DomainException $e) {
			$this->error_response($e->getMessage(), $e->getCode());
		} catch(Exception $e) {
			$this->error_response('Failed to delete media: ' . $e->getMessage(), 500);
		}
	}


	/**
	 * Get upload error message
	 * Only HTTP-specific utility method
	 */
	private function get_upload_error_message(int $error_code): string {
		return match($error_code) {
			UPLOAD_ERR_INI_SIZE => 'File too large (exceeds php.ini limit)',
			UPLOAD_ERR_FORM_SIZE => 'File too large (exceeds form limit)',
			UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
			UPLOAD_ERR_NO_FILE => 'No file was uploaded',
			UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
			UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
			UPLOAD_ERR_EXTENSION => 'Upload stopped by extension',
			default => 'Unknown upload error'
		};
	}
}