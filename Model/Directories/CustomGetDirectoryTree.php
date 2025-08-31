<?php
namespace Icecube\EavManager\Model\Directories;

use Magento\MediaGalleryUi\Model\Directories\GetDirectoryTree as OriginalGetDirectoryTree;
use Magento\Framework\Filesystem;

class GetDirectoryTree extends OriginalGetDirectoryTree
{
    /**
     * Custom execute method
     * 
     * @return array
     */
    public function execute(): array
    {
        // Custom logic to modify the execution of the directory tree
        $tree = [
            'name' => 'root',
            'path' => '/',
            'children' => []
        ];

        // Custom directories logic, calling the modified getDirectories()
        $directories = $this->getDirectories(); 
        foreach ($directories as $idx => &$node) {
            $node['children'] = [];
            $result = $this->findParent($node, $tree);
            $parent = &$result['treeNode'];

            $parent['children'][] = &$directories[$idx];
        }

        return $tree['children'];
    }

    /**
     * Custom getDirectories method
     * 
     * @return array
     */
    private function getDirectories(): array
    {
        $directories = [];
        // Your custom logic to modify the directories
        /** @var Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        if ($mediaDirectory->isDirectory()) {
            // Your custom directories fetching logic, e.g. skip specific paths
            $imageFolderPaths = $this->coreConfig->getValue(
                self::XML_PATH_MEDIA_GALLERY_IMAGE_FOLDERS,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            );
            sort($imageFolderPaths);

            foreach ($imageFolderPaths as $imageFolderPath) {
                $imageDirectory = $this->filesystem->getDirectoryReadByPath(
                    $mediaDirectory->getAbsolutePath($imageFolderPath)
                );
                if ($imageDirectory->isDirectory()) {
                    $directories[] = $this->getDirectoryData($imageFolderPath);
                    foreach ($imageDirectory->readRecursively() as $path) {
                        if ($imageDirectory->isDirectory($path)) {
                            // Custom filter logic (e.g. skipping specific paths)
                           echo "this";
                            exit;
                            $directories[] = $this->getDirectoryData(
                                $mediaDirectory->getRelativePath($imageDirectory->getAbsolutePath($path))
                            );
                        }
                    }
                }
            }
        }

        return $directories;
    }

    /**
     * Return jstree data for given path
     *
     * @param string $path
     * @return array
     */
    private function getDirectoryData(string $path): array
    {
        $pathArray = explode('/', $path);
        return [
            'text' => count($pathArray) > 0 ? end($pathArray) : $path,
            'id' => $path,
            'li_attr' => ['data-id' => $path],
            'path' => $path,
            'path_array' => $pathArray
        ];
    }
}
