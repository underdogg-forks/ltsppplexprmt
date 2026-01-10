<?php

namespace App\Filament\Actions\LetsPeppol\App\Documents;

use App\Filament\Actions\LetsPeppol\BaseLetsPeppolAction;
use Filament\Forms\Components\TextInput;

/**
 * Filament action to delete a document
 * 
 * @covers \App\Services\LetsPeppol\Endpoints\App\DocumentsEndpoint::delete
 */
class DeleteDocumentAction extends BaseLetsPeppolAction
{
    public static function getDefaultName(): ?string
    {
        return 'delete_document';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Delete Document')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Delete Document')
            ->modalDescription('Are you sure you want to delete this document? This action cannot be undone.')
            ->form([
                TextInput::make('id')
                    ->label('Document ID')
                    ->required()
                    ->hint('Enter the document ID to delete'),
            ])
            ->action(function (array $data): void {
                try {
                    $this->getClient()
                        ->app()
                        ->documents()
                        ->delete($data['id']);

                    $this->notifySuccess(
                        'Document Deleted',
                        'Document ' . $data['id'] . ' has been deleted successfully.'
                    );
                } catch (\Exception $e) {
                    $this->notifyError(
                        'Failed to Delete Document',
                        $e->getMessage()
                    );
                }
            });
    }
}
