<?php

namespace App\Filament\Actions\LetsPeppol\App\Documents;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\Textarea;

/**
 * Filament action to validate UBL XML
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\App\DocumentsEndpoint::validate
 */
class ValidateDocumentAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'validate_document';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Validate UBL XML')
            ->icon('heroicon-o-check-badge')
            ->form([
                Textarea::make('ubl_xml')
                    ->label('UBL XML Content')
                    ->required()
                    ->rows(10)
                    ->hint('Paste the UBL XML content to validate'),
            ])
            ->action(function (array $data): void {
                try {
                    $result = $this->getClient()
                        ->app()
                        ->documents()
                        ->validate($data['ubl_xml']);

                    if ($result['valid'] ?? false) {
                        $this->notifySuccess(
                            'Validation Successful',
                            'The UBL XML is valid.'
                        );
                    } else {
                        // Normalize errors to ensure it's always an array
                        $errorData = $result['errors'] ?? ['Unknown error'];
                        $errors = is_array($errorData) ? $errorData : [$errorData];
                        
                        $errorsString = implode(', ', $errors);
                        $this->notifyError(
                            'Validation Failed',
                            'Errors: ' . $errorsString
                        );
                    }
                } catch (\Throwable $e) {
                    $this->notifyError(
                        'Validation Error',
                        $e->getMessage()
                    );
                }
            });
    }
}
