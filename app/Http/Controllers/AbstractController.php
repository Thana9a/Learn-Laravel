<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

abstract class AbstractController extends Controller
{
    public function __construct(
        protected readonly BaseRepositoryInterface $repository
    ) {}

    /**
     * Get the Inertia view base path (e.g. 'Orders' or 'Products').
     */
    abstract protected function viewPath(): string;

    /**
     * Get the route name prefix (e.g. 'orders' or 'products').
     */
    abstract protected function routeName(): string;

    /**
     * Get the translation key domain (e.g. 'order' or 'product').
     */
    abstract protected function langKey(): string;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status', 'sort_by', 'sort_direction']);
        $paginator = $this->repository->paginate(15, ['*'], $filters);

        return Inertia::render("{$this->viewPath()}/Index", [
            'items' => $paginator,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): Response
    {
        $item = $this->repository->findOrFail($id);

        return Inertia::render("{$this->viewPath()}/Show", [
            'item' => $item,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->repository->delete($id);

        return redirect()->route("{$this->routeName()}.index")->with(
            'success',
            __("{$this->langKey()}.deleted")
        );
    }
}
