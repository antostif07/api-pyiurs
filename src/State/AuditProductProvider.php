<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\AuditProduct;
use App\Repository\AuditRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\RequestStack;

class AuditProductProvider implements ProviderInterface
{
    private RequestStack $requestStack;

    public function __construct(
        RequestStack $requestStack,
        private readonly AuditRepository $auditRepository,
        private readonly Pagination $pagination
    )
    {
        $this->requestStack = $requestStack;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();
        $auditId = $request->query->get('auditId');
        $type = $request->query->get('type');
        $dir = str_replace("\\", "/", dirname(__DIR__, 2));

        if(!$auditId) {
            return [];
        }

        $audit = $this->auditRepository->findOneBy(['id' => $auditId]);

        if(!$audit) {
            return [];
        }

        if($type === 'base') {
            if($audit->getBaseFile()) {
                $excelFilePath = $dir.'/public/media/'.$audit->getBaseFile()->filePath;

                $resultFilePath = $audit->getResultFile() ? $dir.'/public/media/'.$audit->getResultFile()->filePath : null;

                $auditProducts = $this->excelToAuditProduct($excelFilePath, $type);
                $resultAuditProducts = $resultFilePath ? $this->excelToAuditProduct($resultFilePath, "result") : [];

                array_shift($auditProducts);
                array_shift($resultAuditProducts);

                $resultBarcodes = array_map(fn($item) => $item->barcode, $resultAuditProducts);
                $baseProducts = array_map(function ($product) use ($resultBarcodes){
                    $productExist = in_array($product->barcode, $resultBarcodes);
                    if($productExist) {
                        $product->status = 'FIND';
                        return $product;
                    }
                    $product->status = 'NOT-FIND';
                    return $product;

                }, $auditProducts);

                $currentPage = $this->pagination->getPage($context);
                $itemPerPage = $this->pagination->getLimit($operation, $context);
                $offset = $this->pagination->getOffset($operation, $context);
                $totalItems = count($baseProducts);

                $pagedAuditProducts = $this->getAuditProductPage($offset, $itemPerPage, $baseProducts);

                return new TraversablePaginator(
                    new \ArrayIterator($pagedAuditProducts), $currentPage, $itemPerPage, $totalItems
                );
            }
        }

        if($type === 'result') {
            if($audit->getResultFile()) {
                $resultFilePath = $dir.'/public/media/'.$audit->getResultFile()->filePath;

                $auditProducts = $this->excelToAuditProduct($resultFilePath, $type);

                array_shift($auditProducts);

                $currentPage = $this->pagination->getPage($context);
                $itemPerPage = $this->pagination->getLimit($operation, $context);
                $offset = $this->pagination->getOffset($operation, $context);
                $totalItems = count($auditProducts);

                $pagedAuditProducts = $this->getAuditProductPage($offset, $itemPerPage, $auditProducts);

                return new TraversablePaginator(
                    new \ArrayIterator($pagedAuditProducts), $currentPage, $itemPerPage, $totalItems
                );
            }
        }

        return [];
    }

    private function excelToAuditProduct($excel_path, $type): array
    {
        $spreadSheet = IOFactory::load($excel_path);
        $worksheet = $spreadSheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $auditProducts = [];

        $i = 0;
        if($type === "base") {
            foreach ($rows as $row) {
                $ap = new AuditProduct();

                $ap->id = $i;
                $ap->barcode = strtoupper(trim($row[0]));
                $ap->place = $row[1];
                $ap->hs_code = $row[2];
                $ap->name = $row[3];
                $ap->description = $row[4];
                $ap->pos_category = $row[5];
                $ap->category = $row[6];
                $ap->quantity = intval($row[7]);
                $ap->price = gettype($row[8]) === 'string' ? floatval(str_replace('$', "", $row[8])) : $row[8];

                $auditProducts[] = $ap;

                $i++;
            }
        }

        if($type === "result") {
            foreach ($rows as $row) {
                $ap = new AuditProduct();

                $ap->id = $i;
                $ap->barcode = strtoupper(trim($row[0]));
                $ap->quantity = 1;

                $auditProducts[] = $ap;

                $i++;
            }
        }

        return $auditProducts;
    }

    private function getAuditProductPage(int $offset, int $limit, $auditProducts): array
    {
        if ($offset < 0 || $limit < 0) {
            return [];
        }

        return array_slice($auditProducts, $offset, $limit);
    }
}
