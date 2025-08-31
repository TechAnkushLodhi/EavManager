<?php

namespace Icecube\EavManager\Plugin;

use Magento\MediaGalleryUi\Model\Directories\GetDirectoryTree;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem;

class GetDirectoryTreePlugin
{
    private $filesystem;
    private $coreConfig;

    public function __construct(
        Filesystem $filesystem,
        ScopeConfigInterface $coreConfig
    ) {
        $this->filesystem = $filesystem;
        $this->coreConfig = $coreConfig;
    }

    public function aroundExecute(GetDirectoryTree $subject, callable $proceed): array
    {
        $tree = [
            'name' => 'root',
            'path' => '/',
            'children' => []
        ];

        $directories = [];

        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        if ($mediaDirectory->isDirectory()) {
            $imageFolderPaths = $this->coreConfig->getValue(
                'system/media_storage_configuration/allowed_resources/media_gallery_image_folders',
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            );
            sort($imageFolderPaths);

            foreach ($imageFolderPaths as $imageFolderPath) {
                $imageDirectory = $this->filesystem->getDirectoryReadByPath(
                    $mediaDirectory->getAbsolutePath($imageFolderPath)
                );

                if ($imageDirectory->isDirectory()) {
                    $directories[] = $this->customGetDirectoryData($imageFolderPath);
                    foreach ($imageDirectory->readRecursively() as $path) {
                        if ($imageDirectory->isDirectory($path)) {
                            // echo "this";
                            // exit;
                            $directories[] = $this->customGetDirectoryData(
                                $mediaDirectory->getRelativePath($imageDirectory->getAbsolutePath($path))
                            );
                        }
                    }
                }
            }
        }

        foreach ($directories as $idx => &$node) {
            $node['children'] = [];
            $result = $this->findParent($node, $tree);
            $parent = &$result['treeNode'];
            $parent['children'][] = &$directories[$idx];
        }

        return $tree['children'];
    }

    private function customGetDirectoryData(string $path): array
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

    private function findParent(array &$node, array &$treeNode, int $level = 0): array
    {
        $nodePathLength = count($node['path_array']);
        $treeNodeParentLevel = $nodePathLength - 1;

        $result = ['treeNode' => &$treeNode];

        if ($nodePathLength <= 1 || $level > $treeNodeParentLevel) {
            return $result;
        }

        foreach ($treeNode['children'] as &$tnode) {
            $tNodePathLength = count($tnode['path_array']);
            $found = false;
            while ($level < $tNodePathLength) {
                $found = $node['path_array'][$level] === $tnode['path_array'][$level];
                if ($found) {
                    $level++;
                } else {
                    break;
                }
            }
            if ($found) {
                return $this->findParent($node, $tnode, $level);
            }
        }

        return $result;
    }
}
